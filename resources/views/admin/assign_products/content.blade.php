<div class="message">
</div>

<div class="panel panel-default">
    <div class="panel-heading headerTitle">
        @lang('quickadmin.assign-product.formTitle')
    </div>
    
    <div class="panel-body">
        <div class="row">
            <div class="col-xs-12 form-group">
                {!! Form::label('company_id', trans('quickadmin.assign-product.fields.company').'*', ['class' => 'control-label']) !!}
                {!! Form::select('company_id', $companies, old('company_id'), ['class' => 'form-control select2', 'required' => '','onchange' => 'getAssignedProducts(this)', 'id' => 'assign_company_id','style' => 'width:100%']) !!}
                <p class="help-block"></p>
                @if($errors->has('company_id'))
                    <p class="help-block">
                        {{ $errors->first('company_id') }}
                    </p>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 form-group">
                {!! Form::label('product_id', trans('quickadmin.assign-product.fields.product-id').'*', ['class' => 'control-label']) !!}
                <button type="button" class="btn btn-primary btn-xs" id="selectbtn-product_id">
                    {{ trans('quickadmin.qa_select_all') }}
                </button>
                <button type="button" class="btn btn-primary btn-xs" id="deselectbtn-product_id">
                    {{ trans('quickadmin.qa_deselect_all') }}
                </button>
                {!! Form::select('product_id[]', $product_ids, old('product_id'), ['class' => 'form-control select2', 'multiple' => 'multiple', 'id' => 'selectall-product_id' , 'required' => '','style' => 'width:100%']) !!}
                <p class="help-block"></p>
                @if($errors->has('product_id'))
                    <p class="help-block">
                        {{ $errors->first('product_id') }}
                    </p>
                @endif
            </div>
        </div>
        
    </div>
</div>