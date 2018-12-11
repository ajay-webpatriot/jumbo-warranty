<tr data-index="{{ $index }}">
    <td>{!! Form::text('service_requests['.$index.'][make]', old('service_requests['.$index.'][make]', isset($field) ? $field->make: ''), ['class' => 'form-control']) !!}</td>
<td>{!! Form::text('service_requests['.$index.'][model_no]', old('service_requests['.$index.'][model_no]', isset($field) ? $field->model_no: ''), ['class' => 'form-control']) !!}</td>
<td>{!! Form::text('service_requests['.$index.'][bill_no]', old('service_requests['.$index.'][bill_no]', isset($field) ? $field->bill_no: ''), ['class' => 'form-control']) !!}</td>
<td>{!! Form::text('service_requests['.$index.'][bill_date]', old('service_requests['.$index.'][bill_date]', isset($field) ? $field->bill_date: ''), ['class' => 'form-control']) !!}</td>
<td>{!! Form::text('service_requests['.$index.'][serial_no]', old('service_requests['.$index.'][serial_no]', isset($field) ? $field->serial_no: ''), ['class' => 'form-control']) !!}</td>
<td>{!! Form::text('service_requests['.$index.'][purchase_from]', old('service_requests['.$index.'][purchase_from]', isset($field) ? $field->purchase_from: ''), ['class' => 'form-control']) !!}</td>
<td>{!! Form::text('service_requests['.$index.'][adavance_amount]', old('service_requests['.$index.'][adavance_amount]', isset($field) ? $field->adavance_amount: ''), ['class' => 'form-control']) !!}</td>
<td>{!! Form::text('service_requests['.$index.'][service_charge]', old('service_requests['.$index.'][service_charge]', isset($field) ? $field->service_charge: ''), ['class' => 'form-control']) !!}</td>
<td>{!! Form::text('service_requests['.$index.'][service_tag]', old('service_requests['.$index.'][service_tag]', isset($field) ? $field->service_tag: ''), ['class' => 'form-control']) !!}</td>
<td>{!! Form::text('service_requests['.$index.'][note]', old('service_requests['.$index.'][note]', isset($field) ? $field->note: ''), ['class' => 'form-control']) !!}</td>
<td>{!! Form::text('service_requests['.$index.'][additional_charges]', old('service_requests['.$index.'][additional_charges]', isset($field) ? $field->additional_charges: ''), ['class' => 'form-control']) !!}</td>
<td>{!! Form::text('service_requests['.$index.'][amount]', old('service_requests['.$index.'][amount]', isset($field) ? $field->amount: ''), ['class' => 'form-control']) !!}</td>

    <td>
        <a href="#" class="remove btn btn-xs btn-danger">@lang('quickadmin.qa_delete')</a>
    </td>
</tr>