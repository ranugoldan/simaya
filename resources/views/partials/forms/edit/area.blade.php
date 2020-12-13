<!-- Area -->
<div class="form-group {{ $errors->has('area') ? ' has-error' : '' }}">
    <label for="area" class="col-md-3 control-label">{{ $translated_name }}</label>
    <div class="col-md-7 col-sm-12{{  (\App\Helpers\Helper::checkIfRequired($item, 'area')) ? ' required' : '' }}">
        <input class="form-control" type="text" name="area" aria-label="area" id="area" value="{{ old('area', $item->area) }}"{!!  (\App\Helpers\Helper::checkIfRequired($item, 'area')) ? ' data-validation="required" required' : '' !!} />
        {!! $errors->first('area', '<span class="alert-msg" aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i> :message</span>') !!}
    </div>
</div>
