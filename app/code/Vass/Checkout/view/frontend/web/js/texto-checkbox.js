require(['jquery'], function($) {
    $(document).ready(function() {
        var added = [false, false, false]; 

        // Función para agregar el <strong> a los <span>
        function addStrongToSpans() {
            var $spanElements = $('span[data-bind="text: description || label"]');

            $spanElements.each(function(index) {
                var $spanElement = $(this);

                if (!added[index] && $spanElement.length) {
                    var strongText, strongId;

                    switch (index) {
                        case 0:
                            strongText = 'Términos y Condiciones.';
                            strongId = 'tyc';
                            break;
                        case 1:
                            strongText = 'Política de Protección de Datos Personales.';
                            strongId = 'privacidad';
                            break;
                        case 2:
                            strongText = 'Comunicaciones de Publicidad y Promociones.';
                            strongId = 'comunicaciones';
                            break;
                    }

                    if (strongText) {
                        $spanElement.html(function(_, html) {
                            return html + ' <strong id="' + strongId + '">' + strongText + '</strong>';
                        });

                        added[index] = true;
                        console.log('Se agregó el <strong> con id "' + strongId + '" al <span> número ' + (index + 1));
                    }
                }
            });
        }

        // Observador para detectar cambios en el DOM
        var observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length || mutation.removedNodes.length) {
                    addStrongToSpans(); 
                }
            });
        });

        // Configurar el observador en el body (o un contenedor más específico si sabes dónde ocurren los cambios)
        observer.observe(document.body, { childList: true, subtree: true });

        // Llamar a la función para buscar los <span> y modificar su contenido
        addStrongToSpans();

        // Función para modificar las clases de los elementos
        function modifyClasses() {
            var $targetDiv = $('div[name="checkout.sidebar.additional.checkbox_privacidad"]');

            if ($targetDiv.length) {
                $targetDiv.removeClass('field _required');
                var $controlDiv = $targetDiv.find('.control');

                if ($controlDiv.length) {
                    $controlDiv.addClass('field _required');
                }
            }
        }

        // Observador para cambios en el DOM para modificar clases
        var observer2 = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length || mutation.removedNodes.length) {
                    modifyClasses(); // Vuelve a intentar modificar clases
                }
            });
        });

        // Configurar el observador en el body
        observer2.observe(document.body, { childList: true, subtree: true });

        // Intentar modificar clases al cargar
        modifyClasses();
    });
});
