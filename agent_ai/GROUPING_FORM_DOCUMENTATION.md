# Point-and-Click AI Agent untuk Pembagian Kelompok

## 📋 Ringkasan

Sistem baru mengubah flow grouping dari **traditional instruction** menjadi **interactive form-based** (point-and-click). User tidak perlu mengetik command kompleks, cukup isi form interaktif dengan pilihan yang mudah dipahami.

---

## 🎯 Alur Sistem

```
┌─────────────────────────────────────────────────────────────────┐
│ 1. USER INPUT: "Buatkan kelompok" (tanpa spesifikasi detail)    │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│ 2. PLANNER: Detect simple grouping request                      │
│    → Action: "clarify_group_requirements"                       │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│ 3. EXECUTOR: Render Interactive Form                             │
│    - Metode pembagian (auto, by grades, by NIM)                 │
│    - Ukuran kelompok (tetap/range)                              │
│    - Constraints opsional (must-together, must-apart)           │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│ 4. USER: Isi Form dan Submit                                    │
│    (Frontend automatically build prompt from form)              │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│ 5. PLANNER: Parse prompt dari form                              │
│    → Action: "process_grouping_form" atau "create_group"        │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│ 6. EXECUTOR: Process & Generate Kelompok                        │
│    → Display hasil dengan buttons (save/refresh)                │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│ 7. USER: Pilih action                                           │
│    - Save ke Database                                           │
│    - Refresh/Acak Ulang (keep specification)                    │
│    - Cancel                                                     │
└─────────────────────────────────────────────────────────────────┘
```

---

## 🏗️ Komponen Teknis

### 1. **GroupingFormHandler** (`nodes/grouping_form_handler.py`)

Utility class untuk handle form logic:

#### Methods:

- **`detect_grouping_request(prompt: str) -> bool`**
  - Deteksi apakah user request pembagian kelompok tanpa detail
  - Return `True` jika user bilang "Buatkan kelompok" (simple)
  - Return `False` jika user sudah kasih detail ("5 orang per kelompok", etc)

- **`generate_form_html(context: Dict) -> str`**
  - Generate HTML form dengan 3 section:
    1. Metode pembagian
    2. Ukuran kelompok
    3. Constraints opsional
  - Include JavaScript untuk form validation

- **`parse_form_submission(form_data: Dict) -> Dict`**
  - Convert form input menjadi structured spec
  - Return dict dengan: method, size_mode, exact_size, min_size, max_size, constraints

- **`build_grouping_prompt(form_spec: Dict) -> str`**
  - Convert form spec menjadi natural language prompt
  - Input ke grouping engine

- **`_parse_constraints(constraint_text: str) -> List[Dict]`**
  - Parse constraint text dengan format:
    - "NIM001 harus sekelompok dengan NIM002"
    - "NIM003 tidak boleh sekelompok dengan NIM004"
  - Return list of constraint objects

### 2. **Planner Node Update** (`nodes/planner_node.py`)

```python
# Import baru
from nodes.grouping_form_handler import GroupingFormHandler

# Logic baru (sebelum hybrid constraint check):
if GroupingFormHandler.detect_grouping_request(prompt):
    plan = {
        "action": "clarify_group_requirements",
        "confidence": 0.98,
        ...
    }
```

**Deteksi:**

- Keywords: "buatkan kelompok", "buat kelompok", "bagi kelompok", dll
- TAPI tanpa detail: tidak ada "berdasarkan nilai", "5 orang", "minimal", etc

### 3. **Executor Node Update** (`nodes/executor_node.py`)

#### Action: `clarify_group_requirements`

```python
elif action == "clarify_group_requirements":
    # Generate dan tampilkan form HTML
    form_html = GroupingFormHandler.generate_form_html(context)
    state["result"] = form_html
    state["grouping_form_shown"] = True
```

#### Action: `process_grouping_form`

```python
elif action == "process_grouping_form":
    # 1. Parse form data dari request
    form_spec = GroupingFormHandler.parse_form_submission(form_data)

    # 2. Build prompt dari form
    prompt = GroupingFormHandler.build_grouping_prompt(form_spec)

    # 3. Execute grouping dengan prompt
    result = create_group(prompt, ...)
```

---

## 🎨 Form Components

### 1. **Metode Pembagian Kelompok**

- **Acak Otomatis**: Random assignment, tidak terstruktur
- **Berdasarkan Nilai**: Kelompok balanced berdasarkan GPA/rata-rata
- **Berdasarkan NIM**: Assign dari NIM awal hingga akhir

### 2. **Ukuran Kelompok**

- **Ukuran Tetap**: Semua kelompok sama size (e.g., 5 orang)
- **Range Anggota**: Minimal-maksimal range (e.g., 4-6 orang)

### 3. **Constraints (Opsional)**

Constraint format (text area):

```
NIM001 harus sekelompok dengan NIM002
NIM003 tidak boleh sekelompok dengan NIM004
NIM005 dan NIM006 harus berbeda kelompok
```

---

## 💡 Contoh Penggunaan

### Scenario 1: User Simple Request

```
User: "Buatkan kelompok"
Agent: [Tampilkan form interaktif]
User: [Isi form dengan opsi-opsi]
User: [Click "Generate Kelompok"]
Agent: [Tampilkan hasil kelompok]
User: [Click "Simpan" atau "Acak Ulang"]
```

### Scenario 2: User Detailed Request (Bypass Form)

```
User: "Buatkan kelompok dengan 5 orang berdasarkan nilai"
Agent: [Langsung execute, skip form]
Agent: [Tampilkan hasil]
```

### Scenario 3: Constraints via Form

```
User: "Buatkan kelompok"
Agent: [Tampilkan form]
User: [Fill form dengan constraints]
     Constraints:
     - NIM001 harus sekelompok dengan NIM002
     - NIM003 tidak boleh sekelompok dengan NIM004
Agent: [Generate kelompok dengan constraints]
```

---

## 🔄 Flow Detail

### Step 1: Detect Simple Grouping Request

```python
# In planner_node.py
if GroupingFormHandler.detect_grouping_request(prompt):
    # User: "Buatkan kelompok" (tanpa detail)
    # → Action: clarify_group_requirements
```

### Step 2: Render Form

```python
# In executor_node.py
form_html = GroupingFormHandler.generate_form_html(context)
# Return HTML dengan form interaktif
```

### Step 3: Form Submission (Frontend)

```javascript
// Form JavaScript menangkap submit
// Build prompt dari form fields
// Kirim ke agent dengan prompt yang di-generate
```

### Step 4: Process Form Submission

```python
# In executor_node.py
form_spec = GroupingFormHandler.parse_form_submission(form_data)
prompt = GroupingFormHandler.build_grouping_prompt(form_spec)
result = create_group(prompt, ...)
```

---

## 📊 Data Flow

### Form Data Structure

```python
{
    "method": "by_grades",           # auto | by_grades | by_nim
    "sizeMode": "range",              # exact | range
    "exactSize": 5,                   # (jika sizeMode=exact)
    "minSize": 4,                     # (jika sizeMode=range)
    "maxSize": 6,                     # (jika sizeMode=range)
    "constraints": "NIM001 harus...",  # text constraints
}
```

### Form Spec (Parsed)

```python
{
    "method": "by_grades",
    "size_mode": "range",
    "exact_size": None,
    "min_size": 4,
    "max_size": 6,
    "constraints": [
        {
            "type": "must_together",
            "student1": "NIM001",
            "student2": "NIM002"
        },
        {
            "type": "must_apart",
            "student1": "NIM003",
            "student2": "NIM004"
        }
    ]
}
```

### Generated Prompt

```
Buatkan kelompok dengan spesifikasi berikut:
- Metode: Berdasarkan nilai rata-rata mahasiswa
- Ukuran: minimal 4 orang, maksimal 6 orang per kelompok
- Constraint:
  • NIM001 harus sekelompok dengan NIM002
  • NIM003 tidak boleh sekelompok dengan NIM004
```

---

## 🚀 Deployment

### Files Modified:

1. ✅ `nodes/planner_node.py` - Add import & detection logic
2. ✅ `nodes/executor_node.py` - Add handlers & import

### Files Created:

1. ✅ `nodes/grouping_form_handler.py` - Form handler

### No changes needed:

- `tools/grouping.py` - Existing grouping engine works as-is
- `tools/grouping_by_grades.py` - Existing engine works as-is
- `tools/grouping_hybrid.py` - Existing engine works as-is

---

## ⚙️ Configuration

### Customize Form (Optional)

Edit `GroupingFormHandler.generate_form_html()` untuk:

- Change colors/styling
- Add more methods
- Add pre-defined constraint templates
- Change min/max size limits

### Customize Constraint Parsing

Edit `_parse_constraints()` untuk support format baru:

```python
# Example: Add support for new format
"NIM001, NIM002 harus satu" → parse sebagai must_together
```

---

## 🐛 Troubleshooting

### Form tidak muncul

- Check: `detect_grouping_request()` return True?
- Check: `action == "clarify_group_requirements"`?
- Check: Browser console untuk error

### Constraint tidak di-parse

- Check constraint format sesuai dengan regex pattern?
- Check: Space dan case sensitivity
- Contoh valid: "NIM001 harus sekelompok dengan NIM002"

### Grouping gagal setelah form submit

- Check: Generated prompt valid?
- Check: Existing groups conflict?
- Check: Student constraints konflik dengan data?

---

## 📚 Integration Checklist

- [ ] Copy `grouping_form_handler.py` ke `nodes/`
- [ ] Update `planner_node.py` (add import + detection)
- [ ] Update `executor_node.py` (add import + handlers)
- [ ] Test dengan simple request: "Buatkan kelompok"
- [ ] Test form submission dengan berbagai options
- [ ] Test constraint parsing
- [ ] Verify backward compatibility (detailed requests masih jalan)

---

## 🎓 Learning Resources

- How form detection works: `GroupingFormHandler.detect_grouping_request()`
- Form HTML generation: `generate_form_html()`
- Constraint parsing: `_parse_constraints()`
- Prompt building: `build_grouping_prompt()`

---

Selamat! Sistem point-and-click grouping agent sudah siap digunakan. 🎉
