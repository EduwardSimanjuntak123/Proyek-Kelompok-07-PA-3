#!/usr/bin/env python3
"""Debug form generation"""
import sys
import os
sys.path.insert(0, 'd:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3')
sys.path.insert(0, 'd:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\agent_ai')
os.chdir('d:\\semester 6\\PROYEK AKHIR 3\\Proyek-Kelompok-07-PA-3\\agent_ai')

from agent_ai.tools.jadwal_seminar import JadwalSeminarTools

# Get the form
form = JadwalSeminarTools.get_form_jadwal()
html = form['message']

# Save to file for inspection - use absolute path
output_file = r'd:\semester 6\PROYEK AKHIR 3\Proyek-Kelompok-07-PA-3\form_debug.html'
with open(output_file, 'w', encoding='utf-8') as f:
    f.write("""<!DOCTYPE html>
<html>
<head><title>Form Debug</title></head>
<body style="font-family: Arial; padding: 20px;">
<h1>Generated Form Debug</h1>
<h2>Form HTML:</h2>
<pre style="background: #f0f0f0; padding: 10px; overflow-x: auto; border: 1px solid #ccc;">""")
    # Escape HTML for display
    f.write(html.replace('&', '&amp;').replace('<', '&lt;').replace('>', '&gt;'))
    f.write("""</pre>

<h2>Form Rendered:</h2>
""")
    f.write(html)
    f.write("""

<script>
console.log('[DEBUG] Page loaded');
setTimeout(() => {
    console.log('[DEBUG] Checking buttons...');
    const addBtn = document.getElementById('add-ruangan-btn');
    const submitBtn = document.getElementById('submit-jadwal-btn');
    console.log('[DEBUG] Add button found:', !!addBtn);
    console.log('[DEBUG] Submit button found:', !!submitBtn);
    if (addBtn) {
        console.log('[DEBUG] Add button text:', addBtn.textContent);
        console.log('[DEBUG] Add button onclick handler:', addBtn.onclick ? 'yes' : 'no');
    }
}, 100);
</script>

</body>
</html>""")

print("✅ Form generated and saved to form_debug.html")
print(f"HTML length: {len(html)} chars")
print(f"\nFirst 500 chars:\n{html[:500]}")
print(f"\nLast 500 chars:\n{html[-500:]}")

# Check for syntax issues
if '{{' in html or '}}' in html:
    print("\n⚠️  WARNING: Found double braces in output!")
    print("First occurrence:", html.find('{{'))
else:
    print("\n✅ No double braces found (good)")

# Check for required elements
required = ["id='jadwal-tanggal'", "id='jadwal-ruangan-container'", "id='add-ruangan-btn'", "id='submit-jadwal-btn'", "id='jadwal-durasi-jam'", "id='jadwal-durasi-menit'"]
for elem in required:
    if elem in html:
        print(f"✅ Found: {elem}")
    else:
        print(f"❌ MISSING: {elem}")
