@extends('user.layout.auth')

@section('content')

<?php $login_user = asset('asset/img/login-user-bg.jpg'); ?>
<div class="full-page-bg" style="background-image: url({{$login_user}});">
<div class="log-overlay"></div>
    <div class="full-page-bg-inner">
        <div class="row no-margin">
            <div class="col-md-6 log-left">
                <span class="login-logo"><img src="{{ Setting::get('site_logo', asset('logo-black.png'))}}"></span>
                <h2>¿Ya tienes una cuenta?</h2>
            </div>
            <div class="col-md-6 log-right">
                <div class="login-box-outer">
                <div class="login-box row no-margin">
                    <div class="col-md-12">
                        <a class="log-blk-btn" href="{{url('login')}}">¿Ya tienes una cuenta?</a>
                        <h3>Crea una nueva cuenta</h3>
                    </div>
                    <form role="form" method="POST" action="{{ url('/register') }}">
                        <div id="first_step">
                            <div class="col-md-4">
                                <input value="{{ env('APP_PHONE_PREFIX', '+91') }}" type="text" placeholder="+1" id="country_code" name="country_code" />
                            </div> 
                            
                            <div class="col-md-8">
                                <input type="text" autofocus id="phone_number" class="form-control" placeholder="Enter Phone Number" name="phone_number" value="{{ old('phone_number') }}" data-stripe="number" maxlength="10" onkeypress="return isNumberKey(event);" />
                            </div>

                            <div class="col-md-8">
                                @if ($errors->has('phone_number'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('phone_number') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="col-md-12" style="padding-bottom: 10px; display: none" id="twilio_sms">
                            </div>

                            <div class="col-md-12" style="padding-bottom: 10px;" id="twilio_sms_btn">
                                <input type="button" class="log-teal-btn small" onclick="twilioSms();" value="Verifica tu número de teléfono"/>
                            </div>
                        </div>

                        <div id="second_step" style="display: none;">
                            <div class="col-md-12">
                                <input type="text" autofocus id="phone_code" class="form-control" placeholder="Enter Phone Code" data-stripe="number" maxlength="6" onkeypress="return isCodeKey(event);" />
                            </div>

                            <div class="col-md-12" style="padding-bottom: 10px; display: none" id="twilio_code">
                            </div>

                            <div class="col-md-12" style="padding-bottom: 10px;" id="twilio_code_btn" >
                                <input type="button" class="log-teal-btn small" onclick="twilioCode();" value="Verifica tu número de teléfono"/>
                            </div>
                        </div>

                        {{ csrf_field() }}

                        <div id="third_step" style="display: none;">
                            <div class="col-md-6">
                                <input type="text" class="form-control" placeholder="First Name" name="first_name" value="{{ old('first_name') }}" data-validation="alphanumeric" data-validation-allowing=" -" data-validation-error-msg="First Name can only contain alphanumeric characters and . - spaces">

                                @if ($errors->has('first_name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('first_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control" placeholder="Last Name" name="last_name" value="{{ old('last_name') }}" data-validation="alphanumeric" data-validation-allowing=" -" data-validation-error-msg="Last Name can only contain alphanumeric characters and . - spaces">

                                @if ($errors->has('last_name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('last_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="col-md-12">
                                <input type="email" class="form-control" name="email" placeholder="Email Address" value="{{ old('email') }}" data-validation="email">

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif                        
                            </div>

                            <div class="col-md-12">
                                <label class="checkbox-inline"><input type="checkbox" name="gender" value="MALE" data-validation="checkbox_group"  data-validation-qty="1" data-validation-error-msg="Please choose one gender">Male</label>
                                <label class="checkbox-inline"><input type="checkbox" name="gender"  value="FEMALE" data-validation="checkbox_group"  data-validation-qty="1" data-validation-error-msg="Please choose one gender">Female</label>
                                @if ($errors->has('gender'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('gender') }}</strong>
                                    </span>
                                @endif                        
                            </div>

                            <div class="col-md-12">
                                <input type="password" class="form-control" name="password" placeholder="Password" data-validation="length" data-validation-length="min6" data-validation-error-msg="Password should not be less than 6 characters">

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="col-md-12">
                                <input type="password" placeholder="Re-type Password" class="form-control" name="password_confirmation" data-validation="confirmation" data-validation-confirm="password" data-validation-error-msg="Confirm Passsword is not matched">

                                @if ($errors->has('password_confirmation'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                @endif
                            </div>
                            
                            <div class="col-md-12">
                                <button class="log-teal-btn" type="submit">REGISTER</button>
                            </div>

                        </div>

                    </form>     

                    <div class="col-md-12">
                        <p class="helper">O <a href="{{route('login')}}">inicie sesión</a> con su cuenta de usuario.</p>
                    </div>

                </div>


                <div class="log-copy"><p class="no-margin">{{ Setting::get('site_copyright', '&copy; '.date('Y').' Appoets') }}</p></div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('scripts')
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.26/jquery.form-validator.min.js"></script>
    <script type="text/javascript">
        $.validate({
            modules : 'security',
        });
        $('.checkbox-inline').on('change', function() {
            $('.checkbox-inline').not(this).prop('checked', false);
        });
        function isNumberKey(evt)
        {
            var edValue = document.getElementById("phone_number");
            var s = edValue.value;
            if (event.keyCode == 13) {
                event.preventDefault();
                if(s.length>=10){
                    smsLogin();
                }
            }
            var charCode = (evt.which) ? evt.which : event.keyCode;
            if (charCode != 46 && charCode > 31
            && (charCode < 48 || charCode > 57))
                return false;

            return true;
        }
        function isCodeKey(evt)
        {
            var edValue = document.getElementById("phone_code");
            var s = edValue.value;
            if (event.keyCode == 13) {
                event.preventDefault();
                if(s.length>=6){
                    twilioCode();
                }
            }
            var charCode = (evt.which) ? evt.which : event.keyCode;
            if (charCode != 46 && charCode > 31
                && (charCode < 48 || charCode > 57))
                return false;

            return true;
        }
    </script>
    <script>
        // login callback
        function twilioCode() {
            var countryCode = document.getElementById("country_code").value;
            var phoneNumber = document.getElementById("phone_number").value;
            var phoneCode = document.getElementById("phone_code").value;
            $('#twilio_code').html("<p class='helper'> Please Wait... </p>");
            $.ajax({
                url: '{{route('twilio.code')}}',
                dataType: 'JSON',
                headers: {'X-CSRF-TOKEN': $('input[name="_token"]').val() },
                type: 'POST',
                data:  { country: countryCode, phone: phoneNumber, code: phoneCode },
                success: function(data) {
                    $('#second_step').fadeOut(400);
                    $('#third_step').fadeIn(400);
                },
                error: function(xhr, status, err) {
                    $('#twilio_code').html("<p class='helper'> " + xhr.responseJSON.message + "</p>").show();
                }
            });
        }

        // phone form submission handler
        function twilioSms() {
            var countryCode = document.getElementById("country_code").value;
            var phoneNumber = document.getElementById("phone_number").value;

            $('#twilio_sms').html("<p class='helper'> Please Wait... </p>").show();
            $('#phone_number').attr('readonly', true);
            $('#country_code').attr('readonly', true);
            $('#twilio_sms_btn').hide();

            $.ajax({
                url: '{{route('twilio.sms')}}',
                dataType: 'JSON',
                headers: {'X-CSRF-TOKEN': $('input[name="_token"]').val() },
                type: 'POST',
                data:  { country: countryCode, phone: phoneNumber },
                success: function(data) {
                    $('#first_step').fadeOut(400);
                    $('#second_step').fadeIn(400);
                },
                error: function(xhr, status, err) {
                    $('#twilio_sms').html("<p class='helper'> " + xhr.responseJSON.message + "</p>").show();
                }
            });
        }
    </script>
@endsection