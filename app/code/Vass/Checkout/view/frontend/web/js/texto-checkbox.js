require(['jquery'], function($) {
    $(document).ready(function() {
        var added = [false, false, false]; // Bandera para asegurar que solo se agregue una vez a cada <span>

        // Función para intentar encontrar los <span> y modificar su contenido
        function addStrongToSpans() {
            // Buscar todos los <span> con el data-bind específico
            var $spanElements = $('span[data-bind="text: description || label"]');

            // Iterar sobre cada <span> encontrado
            $spanElements.each(function(index) {
                var $spanElement = $(this);

                // Verificar si ya se ha agregado el <strong> a este <span>
                if (!added[index] && $spanElement.length) {
                    var strongText, strongId;

                    // Determinar el texto y el id para el <strong> basado en el índice
                    switch (index) {
                        case 0: // Primer <span>
                            strongText = 'Términos y Condiciones.';
                            strongId = 'tyc';
                            break;
                        case 1: // Segundo <span>
                            strongText = 'Política de Protección de Datos Personales.';
                            strongId = 'privacidad';
                            break;
                        case 2: // Tercer <span>
                            strongText = 'Comunicaciones de Publicidad y Promociones.';
                            strongId = 'comunicaciones';
                            break;
                        default:
                            strongText = '';
                            strongId = '';
                    }

                    // Agregar el <strong> al <span> correspondiente
                    if (strongText) {
                        $spanElement.html(function(_, html) {
                            return html + ' <strong id="' + strongId + '">' + strongText + '</strong>';
                        });

                        added[index] = true; // Marcar que ya se ha agregado
                        console.log('Se agregó el <strong> con id "' + strongId + '" al <span> número ' + (index + 1));
                    }
                }
            });

            // Si no se encontraron todos los <span>, intentar de nuevo después de 500 ms
            if ($spanElements.length < 3 || added.includes(false)) {
                setTimeout(addStrongToSpans, 500);
            } else {
                console.log('Todos los <strong> han sido agregados.');
            }
        }

        // Llamar a la función para buscar los <span> y modificar su contenido
        addStrongToSpans();
    });
});
