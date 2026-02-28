@extends('layouts.app')

@section('title', 'Add Student')
@section('header-title', 'Student Admission Form')

@section('content')
<form action="{{ route('students.store') }}" method="post" enctype="multipart/form-data">
    @csrf
    <div class="grid-2">
        <div class="card">
            <h3 class="card-title">1. Personal Details</h3>
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">First Name *</label>
                    <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required>
                    @error('first_name')<span style="color:var(--red);font-size:12px;">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Last Name *</label>
                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" required>
                    @error('last_name')<span style="color:var(--red);font-size:12px;">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Date of Birth</label>
                    <input type="date" name="dob" class="form-control" value="{{ old('dob') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Gender *</label>
                    <select name="gender" class="form-control" required>
                        <option value="">Select</option>
                        <option value="Male" {{ old('gender')=='Male'?'selected':'' }}>Male</option>
                        <option value="Female" {{ old('gender')=='Female'?'selected':'' }}>Female</option>
                        <option value="Other" {{ old('gender')=='Other'?'selected':'' }}>Other</option>
                    </select>
                </div>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Blood Group</label>
                    <select name="blood_group" class="form-control">
                        <option value="">Select</option>
                        @foreach(['A+','A-','B+','B-','O+','O-','AB+','AB-'] as $bg)
                        <option value="{{ $bg }}" {{ old('blood_group')==$bg?'selected':'' }}>{{ $bg }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <input type="text" name="category" class="form-control" value="{{ old('category') }}" placeholder="e.g. General, OBC">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Admission Date</label>
                <input type="date" name="admission_date" class="form-control" value="{{ old('admission_date', date('Y-m-d')) }}">
            </div>
        </div>

        <div class="card">
            <h3 class="card-title">2. Academic Details</h3>
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Class *</label>
                    <select name="class_id" id="class_id" class="form-control" required>
                        <option value="">Select</option>
                        @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ old('class_id')==$c->id?'selected':'' }}>{{ $c->class_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Section *</label>
                    <select name="section_id" id="section_id" class="form-control" required>
                        <option value="">Select Class first</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Roll Number</label>
                <input type="text" name="roll_no" class="form-control" value="{{ old('roll_no') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Admission Number *</label>
                <input type="text" name="admission_no" class="form-control" value="{{ old('admission_no', 'ADM' . str_pad(rand(1,99999), 5, '0', STR_PAD_LEFT)) }}" required>
                @error('admission_no')<span style="color:var(--red);font-size:12px;">{{ $message }}</span>@enderror
            </div>
        </div>
    </div>

    <div class="grid-2">
        <div class="card">
            <h3 class="card-title">3. Parent Details</h3>
            <div class="form-group">
                <label class="form-label">Father Name</label>
                <input type="text" name="father_name" class="form-control" value="{{ old('father_name') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Mother Name</label>
                <input type="text" name="mother_name" class="form-control" value="{{ old('mother_name') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Parent Contact</label>
                <input type="text" name="parent_contact" class="form-control" value="{{ old('parent_contact') }}" placeholder="10 digit mobile">
            </div>
            <div class="form-group">
                <label class="form-label">Alternate Contact</label>
                <input type="text" name="alt_contact" class="form-control" value="{{ old('alt_contact') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}">
            </div>
        </div>

        <div class="card">
            <h3 class="card-title">4. Address</h3>
            <div class="form-group">
                <label class="form-label">Current Address</label>
                <textarea name="current_address" class="form-control" rows="3">{{ old('current_address') }}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Permanent Address</label>
                <textarea name="permanent_address" class="form-control" rows="3">{{ old('permanent_address') }}</textarea>
            </div>
        </div>
    </div>

    <div class="card">
        <h3 class="card-title">5. Photo Upload</h3>
        <div class="form-group">
            <input type="file" name="photo" accept="image/*" class="form-control">
        </div>
        <div class="form-group">
            <label style="display:flex;align-items:center;gap:8px;">
                <input type="checkbox" name="status" value="1" {{ old('status', true) ? 'checked' : '' }}>
                Active
            </label>
        </div>
    </div>

    <div style="display:flex;gap:12px;margin-top:20px;">
        <button type="submit" class="btn btn-primary">Save</button>
        <a href="{{ route('students.index') }}" class="btn btn-secondary">Cancel</a>
        <button type="reset" class="btn btn-secondary">Reset</button>
    </div>
</form>

@push('scripts')
<script>
(function() {
    var classSelect = document.getElementById('class_id');
    var sectionSelect = document.getElementById('section_id');
    var oldSection = '{{ old("section_id") }}';
    var urlBase = '{{ url("/sections-by-class") }}';
    function updateSections() {
        var classId = classSelect.value;
        sectionSelect.innerHTML = '<option value="">' + (classId ? 'Loading...' : 'Select Class first') + '</option>';
        if (!classId) return;
        fetch(urlBase + '/' + classId)
            .then(function(r) { return r.json(); })
            .then(function(sections) {
                sectionSelect.innerHTML = '<option value="">Select</option>';
                sections.forEach(function(sec) {
                    var opt = document.createElement('option');
                    opt.value = sec.id;
                    opt.textContent = sec.name;
                    if (String(sec.id) === String(oldSection)) opt.selected = true;
                    sectionSelect.appendChild(opt);
                });
            })
            .catch(function() {
                sectionSelect.innerHTML = '<option value="">Select</option>';
            });
    }
    classSelect.addEventListener('change', updateSections);
    if (classSelect.value) updateSections();
})();
</script>
@endpush
@endsection
