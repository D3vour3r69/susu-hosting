$(function() {
    const getCleanUnitName = (fullText) => {
        const match = fullText.match(/^(.+?)\s*\(/);
        return match ? match[1].trim() : fullText;
    };

    $('#unit-select').select2({
        theme: "bootstrap-5",
        allowClear: true,
        placeholder: "Подразделения",
        width: '100%',
        templateResult: function(unit) {
            if (!unit.id) return unit.text;

            const cleanName = getCleanUnitName(unit.text);
            const head = $(unit.element).data('head');

            return $(
                `<div>
                    <div class="fw-bold">${cleanName}</div>
                    ${head ? `<div class="text-muted small">Руководитель: ${head}</div>` : ''}
                </div>`
            );
        },
        templateSelection: function(unit) {
            if (!unit.id) return unit.text;
            return getCleanUnitName(unit.text);
        }
    });

    $('#unit-select').on('change', function() {
        $('#unit-filter-form').submit();
    });
});
