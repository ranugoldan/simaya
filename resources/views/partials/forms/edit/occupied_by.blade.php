<!-- Occupied By -->
<div class="form-group {{ $errors->has('occupied_by') ? ' has-error' : '' }}">
    <label for="occupied_by" class="col-md-3 control-label">{{ $translated_name }}</label>
    <div class="col-md-7 col-sm-12{{  (\App\Helpers\Helper::checkIfRequired($item, 'occupied_by')) ? ' required' : '' }}">
        <input class="form-control" type="text" name="occupied_by" aria-label="occupied_by" id="occupied_by" value="{{ old('occupied_by', $item->occupied_by) }}"{!!  (\App\Helpers\Helper::checkIfRequired($item, 'occupied_by')) ? ' data-validation="required" required' : '' !!} />
        {!! $errors->first('occupied_by', '<span class="alert-msg" aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i> :message</span>') !!}
    </div>
</div>
