<tr data-index="{{ $index }}">
    <td>{!! Form::text('products['.$index.'][name]', old('products['.$index.'][name]', isset($field) ? $field->name: ''), ['class' => 'form-control']) !!}</td>
<td>{!! Form::text('products['.$index.'][price]', old('products['.$index.'][price]', isset($field) ? $field->price: ''), ['class' => 'form-control']) !!}</td>

    <td>
        <a href="#" class="remove btn btn-xs btn-danger">@lang('quickadmin.qa_delete')</a>
    </td>
</tr>