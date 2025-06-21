$(function() {
    const allResponsibles = window.allResponsibles || [];

    function updateResponsibles(unitId) {
        const $responsibleSelect = $('#responsible_id');
        $responsibleSelect.empty();

        const filteredResponsibles = allResponsibles.filter(user =>
            user.positions.some(position => position.unit_id == unitId)
        );

        if (filteredResponsibles.length === 0) {
            $responsibleSelect.append(
                $('<option></option>').val('').text('Нет доступных ответственных').attr('disabled', true)
            );
            return;
        }

        filteredResponsibles.forEach(user => {
            $responsibleSelect.append(
                $('<option></option>').val(user.id).text(user.name)
            );
        });
    }

    updateResponsibles($('#unit_select').val());

    $('#unit_select').on('change', function() {
        updateResponsibles($(this).val());
    });
});
