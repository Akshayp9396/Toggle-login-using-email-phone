@extends('layout')

@section('content')

<style>
.switch {
  position: relative;
  display: inline-block;
  width: 48px;
  height: 24px;
  vertical-align: middle;
  margin-bottom: 0;
  cursor: pointer;
}
.switch input {
  opacity: 0;
  width: 0;
  height: 0;
  position: absolute;
}
.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #cbd5e1;
  transition: .3s;
  border-radius: 24px;
}
.slider:before {
  position: absolute;
  content: "";
  height: 18px;
  width: 18px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: .3s;
  border-radius: 50%;
  box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}
input:checked + .slider {
  background-color: #2196F3;
}
input:checked + .slider:before {
  transform: translateX(24px);
}
.toggle-status-text {
  margin-left: 12px;
  font-weight: 600;
  font-size: 13px;
  vertical-align: middle;
}
.text-active {
  color: #16a34a;
}
.text-inactive {
  color: #94a3b8;
}
</style>

<div class="col-md-10">
    <section class="panel">
        <header class="panel-heading">
            TOGGLE LOGIN MODE
        </header>

        <div class="panel-body">
            @if(!empty($flash))
                <div class="alert alert-success alert-dismissible fade in" role="alert">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    {{ $flash }}
                </div>
            @endif

            @if(!empty($err))
                <div class="alert alert-danger alert-dismissible fade in" role="alert">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    {{ $err }}
                </div>
            @endif

            <form class="form-horizontal bucket-form" method="post" action="">
                <div class="form-group" style="padding-top: 15px;">
                    <label class="col-sm-3 control-label">Mobile Number Login</label>
                    <div class="col-sm-6" style="padding-top: 5px;">
                        <label class="switch">
                            <input type="radio" name="signup_identifier_type" id="toggle_phone" value="phone" 
                                   onchange="updateSwitchLabels()" {{ $current_mode === 'phone' ? 'checked' : '' }}>
                            <span class="slider round"></span>
                        </label>
                        <span id="label_phone" class="toggle-status-text {{ $current_mode === 'phone' ? 'text-active' : 'text-inactive' }}">
                            {{ $current_mode === 'phone' ? 'Active' : 'Inactive' }}
                        </span>
                        <span class="help-block">Guests will sign up with Name and  Mobile Number.</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label">Email Address Login</label>
                    <div class="col-sm-6" style="padding-top: 5px;">
                        <label class="switch">
                            <input type="radio" name="signup_identifier_type" id="toggle_email" value="email" 
                                   onchange="updateSwitchLabels()" {{ $current_mode === 'email' ? 'checked' : '' }}>
                            <span class="slider round"></span>
                        </label>
                        <span id="label_email" class="toggle-status-text {{ $current_mode === 'email' ? 'text-active' : 'text-inactive' }}">
                            {{ $current_mode === 'email' ? 'Active' : 'Inactive' }}
                        </span>
                        <span class="help-block">Guests will sign up with Name and Email Address.</span>
                    </div>
                </div>

                <div class="form-group" style="margin-top: 25px;">
                    <div class="col-sm-6 col-sm-offset-3">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>

<script type="text/javascript">
function updateSwitchLabels() {
    var phoneRadio = document.getElementById('toggle_phone');
    var emailRadio = document.getElementById('toggle_email');
    var phoneLabel = document.getElementById('label_phone');
    var emailLabel = document.getElementById('label_email');

    if (emailRadio.checked) {
        emailLabel.innerText = 'Active';
        emailLabel.className = 'toggle-status-text text-active';

        phoneLabel.innerText = 'Inactive';
        phoneLabel.className = 'toggle-status-text text-inactive';
    } else {
        phoneLabel.innerText = 'Active';
        phoneLabel.className = 'toggle-status-text text-active';

        emailLabel.innerText = 'Inactive';
        emailLabel.className = 'toggle-status-text text-inactive';
    }
}
</script>

@stop
