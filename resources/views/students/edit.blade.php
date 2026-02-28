@extends('layouts.app')

@section('title', 'Edit Student')
@section('header-title', 'Edit Student')

@section('content')
<form action="{{ route('students.update', $student) }}" method="post" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="grid-2">
        <div class="card">
            <h3 class="card-title">1. Personal Details</h3>
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">First Name *</label>
                    <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $student->first_name) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Last Name *</label>
                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $student->last_name) }}" required>
                </div>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" name="dob" class="form-control" value="{{ old('dob', $student->dob?->format('Y-m-d')) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Gender *</label>
                    <select name="gender" class="form-control" required>
                        @foreach(['Male','Female','Other'] as $g)
                        <option value="{{ $g }}" {{ old('gender', $student->gender)==$g?'selected':'' }}>{{ $g }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Blood Group</label>
                    <select name="blood_group" class="form-control">
                        <option value="">Select</option>
                        @foreach(['A+','A-','B+','B-','O+','O-','AB+','AB-'] as $bg)
                        <option value="{{ $bg }}" {{ old('blood_group', $student->blood_group)==$bg?'selected':'' }}>{{ $bg }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <input type="text" name="category" class="form-control" value="{{ old('category', $student->category) }}">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Admission Date</label>
                <input type="date" name="admission_date" class="form-control" value="{{ old('admission_date', $student->admission_date?->format('Y-m-d')) }}">
            </div>
        </div>

        <div class="card">
            <h3 class="card-title">2. Academic Details</h3>
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Class *</label>
                    <select name="class_id" id="class_id" class="form-control" required>
                        @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ old('class_id', $student->class_id)==$c->id?'selected':'' }}>{{ $c->class_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Section *</label>
                    <select name="section_id" id="section_id" class="form-control" required>
                        <option value="">Select</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Roll Number</label>
                <input type="text" name="roll_no" class="form-control" value="{{ old('roll_no', $student->roll_no) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Admission Number *</label>
                <input type="text" name="admission_no" class="form-control" value="{{ old('admission_no', $student->admission_no) }}" required>
            </div>
        </div>
    </div>

    <div class="grid-2">
        <div class="card">
            <h3 class="card-title">3. Parent Details</h3>
            <div class="form-group">
                <label class="form-label">Father Name</label>
                <input type="text" name="father_name" class="form-control" value="{{ old('father_name', $student->father_name) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Mother Name</label>
                <input type="text" name="mother_name" class="form-control" value="{{ old('mother_name', $student->mother_name) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Parent Contact</label>
                <input type="text" name="parent_contact" class="form-control" value="{{ old('parent_contact', $student->parent_contact) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Alternate Contact</label>
                <input type="text" name="alt_contact" class="form-control" value="{{ old('alt_contact', $student->alt_contact) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $student->email) }}">
            </div>
        </div>

        <div class="card">
            <h3 class="card-title">4. Address</h3>
            <div class="form-group">
                <label class="form-label">Current Address</label>
                <textarea name="current_address" class="form-control" rows="3">{{ old('current_address', $student->current_address) }}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Permanent Address</label>
                <textarea name="permanent_address" class="form-control" rows="3">{{ old('permanent_address', $student->permanent_address) }}</textarea>
            </div>
        </div>
    </div>

    <div class="card">
        <h3 class="card-title">5. Photo</h3>
        @if($student->photo)
        <img src="{{ asset('storage/' . $student->photo) }}" alt="" style="width:80px;height:80px;border-radius:8px;object-fit:cover;margin-bottom:12px;" onerror="this.style.display='none';this.nextElementSibling.style.display='inline-flex';">
        <span style="display:none;width:80px;height:80px;border-radius:8px;background:var(--gray-200);align-items:center;justify-content:center;font-weight:600;margin-bottom:12px;">{{ substr($student->first_name,0,1) }}</span>
        @endif
        <div id="photo-preview-new" style="width:80px;height:80px;border-radius:8px;background:var(--gray-200);margin-bottom:8px;display:none;overflow:hidden;">
            <img id="photo-preview-new-img" src="" alt="New" style="width:100%;height:100%;object-fit:cover;">
        </div>
        <div class="form-group">
            <input type="file" name="photo" id="photo-input-edit" accept="image/*" class="form-control">
        </div>
        <div class="form-group">
            <label style="display:flex;align-items:center;gap:8px;">
                <input type="checkbox" name="status" value="1" {{ old('status', $student->status) ? 'checked' : '' }}>
                Active
            </label>
        </div>
    </div>

    <div style="display:flex;gap:12px;margin-top:20px;">
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('students.index') }}" class="btn btn-secondary">Cancel</a>
        <a href="{{ route('students.show', $student) }}" class="btn btn-secondary">View Profile</a>
    </div>
</form>
@push('scripts')
<script>
(function() {
    var pi = document.getElementById('photo-input-edit');
    var pp = document.getElementById('photo-preview-new');
    var ppi = document.getElementById('photo-preview-new-img');
    if (pi && pp && ppi) {
        pi.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                var r = new FileReader();
                r.onload = function() { ppi.src = r.result; pp.style.display = 'block'; };
                r.readAsDataURL(this.files[0]);
            } else { pp.style.display = 'none'; }
        });
    }
})();
(function() {
    var classSelect = document.getElementById('class_id');
    var sectionSelect = document.getElementById('section_id');
    var selectedSection = '{{ old("section_id", $student->section_id) }}';
    var urlBase = '{{ url("/sections-by-class") }}';
    function updateSections() {
        var classId = classSelect.value;
        sectionSelect.innerHTML = '<option value="">' + (classId ? 'Loading...' : 'Select') + '</option>';
        if (!classId) return;
        fetch(urlBase + '/' + classId)
            .then(function(r) { return r.json(); })
            .then(function(sections) {
                sectionSelect.innerHTML = '<option value="">Select</option>';
                sections.forEach(function(sec) {
                    var opt = document.createElement('option');
                    opt.value = sec.id;
                    opt.textContent = sec.name;
                    if (String(sec.id) === String(selectedSection)) opt.selected = true;
                    sectionSelect.appendChild(opt);
                });
            })
            .catch(function() {
                sectionSelect.innerHTML = '<option value="">Select</option>';
            });
    }
    classSelect.addEventListener('change', updateSections);
    updateSections();
})();
</script>
@endpush
@endsection
