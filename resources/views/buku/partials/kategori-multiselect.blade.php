@props([
    'daftarOpsi' => collect(),
    'selectedOpsi' => [],
    'inputId' => 'opsi',
    'fieldName' => 'opsi',
    'errorKey' => 'opsi',
    'optionValueKey' => 'id',
    'optionLabelKey' => 'nama_kategori',
    'searchPlaceholder' => 'Cari data...',
    'labelSingular' => 'item',
])

<div class="kategori-picker" data-multiselect>
    <div class="d-flex flex-column flex-md-row gap-2 mb-2">
        <input
            type="text"
            class="form-control"
            placeholder="{{ $searchPlaceholder }}"
            data-multiselect-search
            autocomplete="off"
        >
        <div class="d-flex gap-2 flex-shrink-0">
            <button type="button" class="btn btn-outline-primary" data-multiselect-select-visible>Pilih Hasil</button>
            <button type="button" class="btn btn-outline-secondary" data-multiselect-clear>Reset</button>
        </div>
    </div>

    <select
        id="{{ $inputId }}"
        name="{{ $fieldName }}[]"
        class="form-select kategori-picker-select @error($errorKey) is-invalid @enderror"
        size="10"
        multiple
        data-multiselect-select
        data-multiselect-label="{{ $labelSingular }}"
    >
        @forelse ($daftarOpsi as $opsi)
            <option
                value="{{ data_get($opsi, $optionValueKey) }}"
                data-search="{{ \Illuminate\Support\Str::lower((string) data_get($opsi, $optionLabelKey)) }}"
                @selected(in_array((int) data_get($opsi, $optionValueKey), array_map('intval', $selectedOpsi), true))
            >
                {{ data_get($opsi, $optionLabelKey) }}
            </option>
        @empty
            <option disabled>Tidak ada data.</option>
        @endforelse
    </select>

    <div class="form-text mt-2" data-multiselect-summary></div>
</div>
