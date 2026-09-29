"""Read-only UI audit: GET public pages, inspect HTML and inventory templates.
Never submits forms, authenticates, or stores response bodies/cookies.
Run from the repository root: python docs/ui-audit-2026-09-29/collect_evidence.py
"""
from pathlib import Path
from urllib.request import urlopen, Request
from urllib.error import HTTPError
from urllib.parse import urljoin, urlparse
from collections import Counter
from html.parser import HTMLParser
from datetime import datetime, timezone
import hashlib
import json
import re
from bs4 import BeautifulSoup

ROOT = Path(__file__).resolve().parents[2]
OUT = Path(__file__).resolve().parent
BASE = 'http://dinhduong.test'

class StructureParser(HTMLParser):
    """Strict structural nesting check; not a complete HTML5 validator."""
    structural = {'div', 'section', 'main', 'header', 'footer', 'form'}
    def __init__(self):
        super().__init__()
        self.stack, self.errors = [], []
    def handle_starttag(self, tag, attrs):
        if tag in self.structural:
            self.stack.append((tag, self.getpos()[0]))
    def handle_endtag(self, tag):
        if tag not in self.structural:
            return
        if not self.stack or self.stack[-1][0] != tag:
            self.errors.append({'line': self.getpos()[0], 'close': tag, 'open_stack': self.stack[-4:]})
        if any(t == tag for t, _ in self.stack):
            while self.stack:
                if self.stack.pop()[0] == tag:
                    break

pages, asset_urls = [], set()
for route in ['/', '/tu-0-5-tuoi', '/tu-5-19-tuoi', '/tu-19-tuoi', '/wizard',
              '/admin', '/admin/auth/login', '/auth/login', '/admin/dashboard/statistics',
              '/who-statistics.php', '/kythuatcando.php', '/huong-dan-danh-gia-dinh-duong.html']:
    try:
        response = urlopen(BASE + route, timeout=20)
        body = response.read().decode('utf8', 'replace')
        soup = BeautifulSoup(body, 'html.parser')
        ids = Counter(x['id'] for x in soup.select('[id]'))
        parser = StructureParser()
        parser.feed(body)
        page = {'route': route, 'status': response.status, 'final_url': response.url,
                'html_characters': len(body), 'duplicate_ids': {k:v for k,v in ids.items() if v > 1},
                'structural_nesting_flags': parser.errors, 'unclosed_structural_tags': parser.stack,
                'html_lang': soup.html.get('lang') if soup.html else None}
        if route in ['/tu-0-5-tuoi', '/tu-5-19-tuoi', '/tu-19-tuoi', '/wizard']:
            form = soup.select_one('form.pro5-form, form#nutrition-form')
            page['form_fields'] = [{k: x.get(k) for k in ['name','id','type','readonly','maxlength','minlength'] if x.has_attr(k)} for x in form.select('input,select,textarea') if x.get('name') != '_token']
            page['inline_defines_nextStep'] = 'function nextStep(' in body
            page['nextStep_buttons'] = len(soup.select('[onclick^="nextStep"]'))
            page['calendar_birth_exists'] = soup.select_one('#calendar-birth') is not None
            page['script_references_calendar_birth'] = "$('#calendar-birth').data('DateTimePicker').maxDate" in body
            page['measurement_labels_associated'] = len(soup.select('.measurement-card label[for]'))
        for x in soup.select('script[src],link[rel="stylesheet"],img[src]'):
            url = urljoin(response.url, x.get('src') or x.get('href'))
            if urlparse(url).netloc == urlparse(BASE).netloc:
                asset_urls.add(url)
        pages.append(page)
    except HTTPError as e:
        error_soup = BeautifulSoup(e.read().decode('utf8', 'replace'), 'html.parser')
        pages.append({'route': route, 'status': e.code,
                      'error_title':error_soup.title.get_text(strip=True) if error_soup.title else None})
    except Exception as e:
        pages.append({'route': route, 'error': str(e)})

assets = []
for url in sorted(asset_urls):
    try:
        r = urlopen(Request(url, method='HEAD'), timeout=15)
        assets.append({'path':urlparse(url).path, 'status':r.status, 'content_type':r.headers.get('Content-Type')})
    except HTTPError as e:
        assets.append({'path':urlparse(url).path, 'status':e.code})
    except Exception as e:
        assets.append({'path':urlparse(url).path, 'error':str(e)})

inventory = []
for f in sorted((ROOT / 'resources/views').rglob('*.blade.php')):
    content = f.read_text(encoding='utf8')
    inventory.append({'path':f.relative_to(ROOT).as_posix(), 'lines':len(content.splitlines()),
        'sha256':hashlib.sha256(f.read_bytes()).hexdigest(),
        'scope':'vendor' if 'vendor' in f.parts else 'backup' if 'backup' in f.parts or 'backup' in f.name else 'application',
        'hidden_lg_only_lines':[i for i,l in enumerate(content.splitlines(),1) if 'd-lg-block d-none' in l],
        'table_responsive_count':content.count('table-responsive'),
        'push_stacks':re.findall(r"@push\(['\"]([^'\"]+)",content),
        'render_stacks':re.findall(r"@stack\(['\"]([^'\"]+)",content)})

source_checks = []
for relative in ['resources/views/form.blade.php', 'resources/views/form-wizard.blade.php',
                 'resources/views/admin/dashboards/index-admin.blade.php',
                 'resources/views/admin/users/create.blade.php', 'public/kythuatcando.php']:
    parser = StructureParser()
    parser.feed((ROOT/relative).read_text(encoding='utf8'))
    source_checks.append({'path':relative,'structural_nesting_flags':parser.errors,
                          'warning':'Source-level heuristic; inspect Blade branches before treating a flag as a defect'})
source_manifest = []
for relative in ['routes/web.php','routes/admin.php','app/Http/Controllers/WebController.php',
                 'public/web/css/form-clean.css','public/web/css/modern-layout.css',
                 'public/web/css/flexbox-grid.css','public/admin-assets/css/admin.css']:
    f = ROOT/relative
    source_manifest.append({'path':relative,'sha256':hashlib.sha256(f.read_bytes()).hexdigest()})
result = {'generated_utc':datetime.now(timezone.utc).isoformat(),
          'method':'HTTP GET/HEAD and source inspection; no browser layout measurement',
          'pages':pages,'assets':assets,'template_inventory':inventory,
          'source_checks':source_checks,'source_manifest':source_manifest}
(OUT/'evidence.json').write_text(json.dumps(result,ensure_ascii=False,indent=2),encoding='utf8')
print(json.dumps({'pages':[{k:p[k] for k in ['route','status','final_url','structural_nesting_flags','inline_defines_nextStep','nextStep_buttons'] if k in p} for p in pages],
                  'asset_count':len(assets),'asset_failures':[x for x in assets if x.get('status') != 200],
                  'templates':len(inventory),'hidden_content_files':[x['path'] for x in inventory if x['hidden_lg_only_lines']]},ensure_ascii=False,indent=2))
