@extends('layouts/default')

{{-- Page title --}}
@section('title')
	@if ($user->id)
		{{ trans('admin/users/table.updateuser') }}
		{{ $user->present()->fullName() }}
	@else
		{{ trans('admin/users/table.createuser') }}
	@endif

@parent
@stop

@section('header_right')
<a href="{{ URL::previous() }}" class="btn btn-primary pull-right">
  {{ trans('general.back') }}</a>
@stop

{{-- Page content --}}
@section('content')

<style>
    .form-horizontal .control-label {
      padding-top: 0px;
    }

    input[type='text'][disabled], input[disabled], textarea[disabled], input[readonly], textarea[readonly], .form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control {
      background-color: white;
      color: #555555;
      cursor:text;
    }
    table.permissions {
      display:flex;
      flex-direction: column;
    }

    .permissions.table > thead, .permissions.table > tbody {
      margin: 15px;
      margin-top: 0px;
    }

    .permissions.table > tbody {
        border: 1px solid;
    }

    .header-row {
      border-bottom: 1px solid #ccc;
    }

    .permissions-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .table > tbody > tr > td.permissions-item {
      padding: 1px;
      padding-left: 8px;
    }

    .header-name {
      cursor: pointer;
    }

</style>

<div class="row">
  <div class="col-md-8 col-md-offset-2">
    <form class="form-horizontal" method="post" autocomplete="off" action="{{ (isset($user->id)) ? route('users.update', ['user' => $user->id]) : route('users.store') }}" enctype="multipart/form-data" id="userForm">
      {{csrf_field()}}

      @if($user->id)
          {{ method_field('PUT') }}
      @endif
        <!-- Custom Tabs -->
      <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
          <li class="active"><a href="#tab_1" data-toggle="tab">Information</a></li>
          <li><a href="#tab_2" data-toggle="tab">Permissions</a></li>
        </ul>

        <div class="tab-content">
          <div class="tab-pane active" id="tab_1">
            <div class="row">
              <div class="col-md-12">
                <!-- First Name -->
                <div class="form-group {{ $errors->has('first_name') ? 'has-error' : '' }}">
                  <label class="col-md-3 control-label" for="first_name">{{ trans('general.first_name') }}</label>
                  <div class="col-md-6{{  (\App\Helpers\Helper::checkIfRequired($user, 'first_name')) ? ' required' : '' }}">
                    <input class="form-control" type="text" name="first_name" id="first_name" value="{{ old('first_name', $user->first_name) }}" />
                    {!! $errors->first('first_name', '<span class="alert-msg" aria-hidden="true">:message</span>') !!}
                  </div>
                </div>


                <!-- Username -->
                <div class="form-group {{ $errors->has('username') ? 'has-error' : '' }}">
                  <label class="col-md-3 control-label" for="username">{{ trans('admin/users/table.username') }}</label>
                  <div class="col-md-6{{  (\App\Helpers\Helper::checkIfRequired($user, 'username')) ? ' required' : '' }}">
                    @if ($user->ldap_import!='1' || str_contains(Route::currentRouteName(), 'clone'))
                      <input
                        class="form-control"
                        type="text"
                        name="username"
                        id="username"
                        value="{{ Request::old('username', $user->username) }}"
                        autocomplete="off"
                        readonly
                        onfocus="this.removeAttribute('readonly');"
                        {{ ((config('app.lock_passwords') && ($user->id)) ? ' disabled' : '') }}
                      >
                      @if (config('app.lock_passwords') && ($user->id))
                        <p class="help-block">{{ trans('admin/users/table.lock_passwords') }}</p>
                      @endif
                    @else
                      (Managed via LDAP)
                          <input type="hidden" name="username" value="{{ Request::old('username', $user->username) }}">

                    @endif

                    {!! $errors->first('username', '<span class="alert-msg" aria-hidden="true">:message</span>') !!}
                  </div>
                </div>

                <!-- Password -->
                <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
                  <label class="col-md-3 control-label" for="password">
                    {{ trans('admin/users/table.password') }}
                  </label>
                  <div class="col-md-6{{  (\App\Helpers\Helper::checkIfRequired($user, 'password')) ? ' required' : '' }}">
                    @if ($user->ldap_import!='1' || str_contains(Route::currentRouteName(), 'clone') )
                      <input
                        type="password"
                        name="password"
                        class="form-control"
                        id="password"
                        value=""
                        autocomplete="off"
                        readonly
                        onfocus="this.removeAttribute('readonly');"
                        {{ ((config('app.lock_passwords') && ($user->id)) ? ' disabled' : '') }}>
                    @else
                      (Managed via LDAP)
                    @endif
                    <span id="generated-password"></span>
                    {!! $errors->first('password', '<span class="alert-msg" aria-hidden="true">:message</span>') !!}
                  </div>
                  <div class="col-md-2">
                    @if ($user->ldap_import!='1')
                      <a href="#" class="left" id="genPassword">Generer passord</a>
                    @endif
                  </div>
                </div>

                @if ($user->ldap_import!='1' || str_contains(Route::currentRouteName(), 'clone'))
                <!-- Password Confirm -->
                <div class="form-group {{ $errors->has('password_confirmation') ? 'has-error' : '' }}">
                  <label class="col-md-3 control-label" for="password_confirmation">
                    {{ trans('admin/users/table.password_confirm') }}
                  </label>
                  <div class="col-md-6{{  ((\App\Helpers\Helper::checkIfRequired($user, 'first_name')) && (!$user->id)) ? ' required' : '' }}">
                    <input
                    type="password"
                    name="password_confirmation"
                    id="password_confirm"
                    class="form-control"
                    value=""
                    autocomplete="off"
                    aria-label="password_confirmation"
                    readonly
                    onfocus="this.removeAttribute('readonly');"
                    {{ ((config('app.lock_passwords') && ($user->id)) ? ' disabled' : '') }}
                    >
                    @if (config('app.lock_passwords') && ($user->id))
                    <p class="help-block">{{ trans('admin/users/table.lock_passwords') }}</p>
                    @endif
                    {!! $errors->first('password_confirmation', '<span class="alert-msg" aria-hidden="true">:message</span>') !!}
                  </div>
                </div>
                @endif

              <!-- Activation Status -->
                  <div class="form-group {{ $errors->has('activated') ? 'has-error' : '' }}">

                      <div class="form-group">
                          <div class="col-md-3 control-label">
                              {{ Form::label('activated', trans('general.login_enabled')) }}
                          </div>
                          <div class="col-md-9">
                              @if (config('app.lock_passwords'))
                                  <div class="icheckbox disabled" style="padding-left: 10px;">
                                      <input type="checkbox" value="1" name="activated" class="minimal disabled" {{ (old('activated', $user->activated)) == '1' ? ' checked="checked"' : '' }} disabled="disabled" aria-label="activated">
                                      <!-- this is necessary because the field is disabled and will reset -->
                                      <input type="hidden" name="activated" value="{{ (int)$user->activated }}">
                                      {{ trans('admin/users/general.activated_help_text') }}
                                      <p class="help-block">{{ trans('general.feature_disabled') }}</p>

                                  </div>
                              @elseif ($user->id === Auth::user()->id)
                                  <div class="icheckbox disabled" style="padding-left: 10px;">
                                      <input type="checkbox" value="1" name="activated" class="minimal disabled" {{ (old('activated', $user->activated)) == '1' ? ' checked="checked"' : '' }} disabled="disabled">
                                      <!-- this is necessary because the field is disabled and will reset -->
                                      <input type="hidden" name="activated" value="1" aria-label="activated">
                                      {{ trans('admin/users/general.activated_help_text') }}
                                      <p class="help-block">{{ trans('admin/users/general.activated_disabled_help_text') }}</p>
                                  </div>
                              @else
                                  <div style="padding-left: 10px;">
                                      <input type="checkbox" value="1" id="activated" name="activated" class="minimal" {{ (old('activated', $user->activated)) == '1' ? ' checked="checked"' : '' }} aria-label="activated">
                                      {{ trans('admin/users/general.activated_help_text') }}
                                  </div>
                              @endif

                              {!! $errors->first('activated', '<span class="alert-msg" aria-hidden="true">:message</span>') !!}

                          </div>
                      </div>
                  </div>


                  <!-- Email -->
                <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                  <label class="col-md-3 control-label" for="email">{{ trans('admin/users/table.email') }} </label>
                  <div class="col-md-6{{  (\App\Helpers\Helper::checkIfRequired($user, 'email')) ? ' required' : '' }}">
                    <input
                      class="form-control"
                      type="text"
                      name="email"
                      id="email"
                      value="{{ Request::old('email', $user->email) }}"
                      {{ ((config('app.lock_passwords') && ($user->id)) ? ' disabled' : '') }}
                      autocomplete="off"
                      readonly
                      onfocus="this.removeAttribute('readonly');">
                    @if (config('app.lock_passwords') && ($user->id))
                    <p class="help-block">{{ trans('admin/users/table.lock_passwords') }}</p>
                    @endif
                    {!! $errors->first('email', '<span class="alert-msg" aria-hidden="true">:message</span>') !!}
                  </div>
                </div>


                  <!-- Email user -->
                  @if (!$user->id)
                      <div class="form-group" id="email_user_row">
                          <div class="col-sm-3">
                          </div>
                          <div class="col-md-9">
                              <div class="icheckbox disabled" id="email_user_div">
                                  {{ Form::checkbox('email_user', '1', Request::old('email_user'),['class' => 'minimal', 'disabled'=>true, 'id' => 'email_user_checkbox']) }}
                                  Send innloggingsinformasjon på epost?

                              </div>
                              <p class="help-block">
                                  Epostadresse må oppgis for å kunne sende innloggingsinformasjon på epost. Passord kan ikke hentes etter lagring.
                              </p>


                          </div>
                      </div> <!--/form-group-->
                  @endif

              <!-- Image -->
                  @if ($user->avatar)
                      <div class="form-group {{ $errors->has('image_delete') ? 'has-error' : '' }}">
                          <label class="col-md-3 control-label" for="image_delete">{{ trans('general.image_delete') }}</label>
                          <div class="col-md-5">
                              {{ Form::checkbox('image_delete') }}

                              <img src="{{ Storage::disk('public')->url(app('users_upload_path').e($user->avatar)) }}" class="img-responsive" />
                              {!! $errors->first('image_delete', '<span class="alert-msg"><br>:message</span>') !!}
                          </div>
                      </div>
                  @endif

                  @include ('partials.forms.edit.image-upload', ['fieldname' => 'avatar'])


                <!-- language -->
                <div class="form-group {{ $errors->has('locale') ? 'has-error' : '' }}">
                  <label class="col-md-3 control-label" for="locale">{{ trans('general.language') }}</label>
                  <div class="col-md-6">
                    {!! Form::locales('locale', old('locale', $user->locale), 'select2') !!}
                    {!! $errors->first('locale', '<span class="alert-msg" aria-hidden="true">:message</span>') !!}
                  </div>
                </div>

      

                @if ($snipeSettings->two_factor_enabled!='')
                  @if ($snipeSettings->two_factor_enabled=='1')
                  <div class="form-group">
                    <div class="col-md-3 control-label">
                      {{ Form::label('two_factor_optin', trans('admin/settings/general.two_factor')) }}
                    </div>
                    <div class="col-md-9">
                        @if (config('app.lock_passwords'))
                            <div class="icheckbox disabled">
                            {{ Form::checkbox('two_factor_optin', '1', Request::old('two_factor_optin', $user->two_factor_optin),['class' => 'minimal', 'disabled'=>'disabled']) }} {{ trans('admin/settings/general.two_factor_enabled_text') }}
                                <p class="help-block">{{ trans('general.feature_disabled') }}</p>
                            </div>
                        @else
                            {{ Form::checkbox('two_factor_optin', '1', Request::old('two_factor_optin', $user->two_factor_optin),['class' => 'minimal']) }} {{ trans('admin/settings/general.two_factor_enabled_text') }}
                            <p class="help-block">{{ trans('admin/users/general.two_factor_admin_optin_help') }}</p>

                        @endif

                    </div>
                  </div>
                  @endif

                  <!-- Reset Two Factor -->
                  <div class="form-group">
                    <div class="col-md-8 col-md-offset-3 two_factor_resetrow">
                      <a class="btn btn-default btn-sm pull-left" id="two_factor_reset" style="margin-right: 10px;"> {{ trans('admin/settings/general.two_factor_reset') }}</a>
                      <span id="two_factor_reseticon">
                      </span>
                      <span id="two_factor_resetresult">
                      </span>
                      <span id="two_factor_resetstatus">
                      </span>
                    </div>
                    <div class="col-md-8 col-md-offset-3 two_factor_resetrow">
                      <p class="help-block">{{ trans('admin/settings/general.two_factor_reset_help') }}</p>
                    </div>
                  </div>
                @endif

                <!-- Notes -->
                <div class="form-group{!! $errors->has('notes') ? ' has-error' : '' !!}">
                  <label for="notes" class="col-md-3 control-label">{{ trans('admin/users/table.notes') }}</label>
                  <div class="col-md-6">
                    <textarea class="form-control" rows="5" id="notes" name="notes">{{ old('notes', $user->notes) }}</textarea>
                    {!! $errors->first('notes', '<span class="alert-msg" aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i> :message</span>') !!}
                  </div>
                </div>

                  <!-- Groups -->
                  <div class="form-group{{ $errors->has('groups') ? ' has-error' : '' }}">
                      <label class="col-md-3 control-label" for="groups[]"> {{ trans('general.groups') }}</label>
                      <div class="col-md-6">

                          @if ((Config::get('app.lock_passwords') || (!Auth::user()->isSuperUser())))

                              @if (count($userGroups->keys()) > 0)
                                  <ul>
                                      @foreach ($groups as $id => $group)
                                          {!! ($userGroups->keys()->contains($id) ? '<li>'.e($group).'</li>' : '') !!}
                                      @endforeach
                                  </ul>
                              @endif

                              <span class="help-block">Only superadmins may edit group memberships.</p>
                                  @else
                                      <div class="controls">
                        <select
                                name="groups[]"
                                aria-label="groups[]"
                                id="groups[]"
                                multiple="multiple"
                                class="form-control">

                            @foreach ($groups as $id => $group)
                                <option value="{{ $id }}"
                                        {{ ($userGroups->keys()->contains($id) ? ' selected="selected"' : '') }}>
                                    {{ $group }}
                                </option>
                            @endforeach
                        </select>

                        <span class="help-block">
                          {{ trans('admin/users/table.groupnotes') }}
                        </span>
                    </div>
                          @endif

                      </div>
                  </div>


              </div> <!--/col-md-12-->
            </div>
          </div><!-- /.tab-pane -->

          <div class="tab-pane" id="tab_2">
            <div class="col-md-12">
              @if (!Auth::user()->isSuperUser())
                <p class="alert alert-warning">Only superadmins may grant a user superadmin access.</p>
              @endif

              @if (!Auth::user()->hasAccess('admin'))
                <p class="alert alert-warning">Only users with admins rights or greater may grant a user admin access.</p>              
              @endif
            </div>

            <table class="table table-striped permissions">
              <thead>
                <tr class="permissions-row">
                  <th class="col-md-5">Permission</th>
                  <th class="col-md-1">Grant</th>
                  <th class="col-md-1">Deny</th>
                  <th class="col-md-1">Inherit</th>
                </tr>
              </thead>
                @include('partials.forms.edit.permissions-base')
            </table>
          </div><!-- /.tab-pane -->
        </div><!-- /.tab-content -->
        <div class="box-footer text-right">
          <button type="submit" class="btn btn-primary"><i class="fa fa-check icon-white" aria-hidden="true"></i> {{ trans('general.save') }}</button>
        </div>
      </div><!-- nav-tabs-custom -->
    </form>
  </div> <!--/col-md-8-->
</div><!--/row-->
@stop

@section('moar_scripts')

<script nonce="{{ csrf_token() }}">
$(document).ready(function() {

    $('#activated').on('ifChecked', function(event){
        console.log('user activated is checked');
        $("#email_user_row").show();
	});

    $('#activated').on('ifUnchecked', function(event){
        $("#email_user_row").hide();
    });

    $('#email').on('keyup',function(){
        event.preventDefault();

        if(this.value.length > 5){
            $('#email_user_checkbox').iCheck('enable');
        } else {
            $('#email_user_checkbox').iCheck('disable').iCheck('uncheck');
        }
    });


	// Check/Uncheck all radio buttons in the group
    $('tr.header-row input:radio').on('ifClicked', function () {
        value = $(this).attr('value');
        area = $(this).data('checker-group');
        $('.radiochecker-'+area+'[value='+value+']').iCheck('check');
    });

    $('.header-name').click(function() {
        $(this).parent().nextUntil('tr.header-row').slideToggle(500);
    });

    $('.tooltip-base').tooltip({container: 'body'})
    $(".superuser").change(function() {
        var perms = $(this).val();
        if (perms =='1') {
            $("#nonadmin").hide();
        } else {
            $("#nonadmin").show();
        }
    });

    $('#genPassword').pGenerator({
        'bind': 'click',
        'passwordElement': '#password',
        'displayElement': '#generated-password',
        'passwordLength': 16,
        'uppercase': true,
        'lowercase': true,
        'numbers':   true,
        'specialChars': true,
        'onPasswordGenerated': function(generatedPassword) {
            $('#password_confirm').val($('#password').val());
        }
    });

    $("#two_factor_reset").click(function(){
        $("#two_factor_resetrow").removeClass('success');
        $("#two_factor_resetrow").removeClass('danger');
        $("#two_factor_resetstatus").html('');
        $("#two_factor_reseticon").html('<i class="fa fa-spinner spin"></i>');
        $.ajax({
            url: '{{ route('api.users.two_factor_reset', ['id'=> $user->id]) }}',
            type: 'POST',
            data: {},
            headers: {
                "X-Requested-With": 'XMLHttpRequest',
                 "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',

            success: function (data) {
                $("#two_factor_reseticon").html('');
                $("#two_factor_resetstatus").html('<i class="fa fa-check text-success"></i>' + data.message);
            },

            error: function (data) {
                $("#two_factor_reseticon").html('');
                $("#two_factor_reseticon").html('<i class="fa fa-exclamation-triangle text-danger"></i>');
                $('#two_factor_resetstatus').text(data.message);
            }


        });
    });


});
</script>


@stop
