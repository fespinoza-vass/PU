require(['jquery'], function($) {
    $(document).ready(function() {
        var added = [false, false, false]; 

        // Función para agregar los <strong> a los <span>
        function addStrongToSpans() {
            var $spanElements = $('span[data-bind="text: description || label"]');

            $spanElements.each(function(index) {
                var $spanElement = $(this);

                if (!added[index] && $spanElement.length) {
                    var strongText1, strongId1, strongText2, strongId2;

                    switch (index) {
                        case 0:
                            strongText1 = 'Términos y Condiciones ';
                            strongText2 = 'y la Política de Protección de Datos Personales.';
                            strongId1 = 'tyc';
                            strongId2 = 'privacidad';
                            break;
                        case 1:
                            strongText1 = 'Comunicaciones de Publicidad y Promociones.';
                            strongId1 = 'comunicaciones';
                            break;
                    }

                    if (strongText1) {
                        $spanElement.html(function(_, html) {
                            // Concatenar ambos <strong> en un solo span
                            if (strongText2 && strongId2) {
                                return html + ' <strong id="' + strongId1 + '">' + strongText1 + '</strong> <strong id="' + strongId2 + '">' + strongText2 + '</strong>';
                            } else {
                                return html + ' <strong id="' + strongId1 + '">' + strongText1 + '</strong>';
                            }
                        });

                        added[index] = true;
                        console.log('Se agregaron los <strong> con ids "' + strongId1 + '" y "' + strongId2 + '" al <span> número ' + (index + 1));
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
