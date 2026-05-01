#!/usr/bin/env python3
from tools.pembimbing_tools import _extract_dosen_constraints_from_prompt, generate_pembimbing_assignments_by_context

prompt = 'Buatlah dosen pembimbing'
constraints = _extract_dosen_constraints_from_prompt(prompt)
result = generate_pembimbing_assignments_by_context(constraints=constraints, exclude_disrecommended=True, replace_existing=False, persist=False)
print('Status:', result.get('status'))
if result.get('status') == 'success':
    groups = result.get('groups', [])
    pb2_lektors = []
    for g in groups:
        for p in g.get('pembimbing', []):
            if p['pembimbing_position'] == 2:
                jd = (p.get('jabatan_akademik_desc') or '').lower()
                if 'lektor' in jd:
                    pb2_lektors.append((g['nomor_kelompok'], p['dosen_nama'], p.get('jabatan_akademik_desc')))
    print('PB2 with lektor roles:', pb2_lektors)
else:
    print('Generation failed:', result.get('message'))
