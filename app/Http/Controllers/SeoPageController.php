<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Project;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class SeoPageController extends Controller
{
    public function __invoke(Request $request): View
    {
        $page = (string) $request->route('page');
        $pages = $this->pages();
        abort_unless(isset($pages[$page]), 404);

        $settings = Setting::query()->pluck('value', 'key');

        return view('seo.page', [
            ...$pages[$page],
            'page' => $page,
            'packages' => Package::query()->where('active', true)->orderBy('sort_order')->get(),
            'projects' => Project::query()->where('active', true)->orderBy('sort_order')->get(),
            'whatsapp' => preg_replace('/\D+/', '', (string) ($settings->get('whatsapp_number') ?: config('xpertsystems.whatsapp_number', ''))),
            'contactEmail' => $settings->get('contact_email') ?: config('xpertsystems.contact_email', ''),
        ]);
    }

    /** @return array<string, array<string, mixed>> */
    private function pages(): array
    {
        return [
            'landing-pages' => [
                'title' => 'Landing Pages para Conseguir Clientes | XpertSystems',
                'description' => 'Diseñamos landing pages rápidas y claras para convertir campañas en prospectos. Incluye WhatsApp, formulario, dominio, hosting y SSL por un año.',
                'eyebrow' => 'Landing pages para campañas y servicios',
                'h1' => 'Convierte tus visitas en prospectos con una landing page clara',
                'lead' => 'Presenta una oferta, responde las dudas importantes y lleva a cada persona hacia una sola acción: escribirte, solicitar información o comprar.',
                'primaryCta' => 'Cotizar mi landing page',
                'introTitle' => 'Una página enfocada en una meta comercial',
                'intro' => 'Una landing page concentra el mensaje de una campaña, producto o servicio sin distraer al visitante. Es ideal si anuncias en Google, Meta o redes sociales y necesitas un lugar propio donde explicar tu propuesta y captar datos.',
                'benefits' => [
                    ['Más conversaciones', 'WhatsApp y formulario aparecen en los momentos donde una persona ya entendió el valor de tu oferta.'],
                    ['Mensaje sin distracciones', 'Ordenamos problema, solución, beneficios, prueba y llamada a la acción alrededor de un objetivo.'],
                    ['Medición preparada', 'Dejamos puntos de medición para conocer clics, formularios y acciones de cotización cuando agregues tu ID de GA4.'],
                    ['Lista para celular', 'Contenido, botones e imágenes se diseñan primero para la pantalla desde la que llegarán la mayoría de tus prospectos.'],
                ],
                'includes' => ['Diseño a medida y adaptable a celular', 'Mensaje comercial y estructura de conversión', 'Botón de WhatsApp y formulario de contacto', 'Enlaces a redes sociales', 'SEO técnico esencial', 'Dominio .com, hosting y SSL durante el primer año'],
                'process' => [['Definimos la oferta', 'Aclaramos qué vendes, a quién y qué acción quieres provocar.'], ['Preparamos el mensaje', 'Organizamos beneficios, objeciones, prueba disponible y llamadas a la acción.'], ['Diseñamos y conectamos', 'Construimos la experiencia, integramos contactos y revisamos en celular.'], ['Publicamos y medimos', 'Dejamos la página lista para campañas y para conectar tus herramientas de analítica.']],
                'faqs' => [
                    ['¿Cuánto cuesta una landing page?', 'El paquete Landing Page parte de $2,700 MXN e incluye la base publicada en la sección de precios. Funciones o integraciones especiales se cotizan aparte.'],
                    ['¿Cuánto tarda?', 'Una landing sencilla puede estar lista en pocos días cuando recibimos a tiempo textos, imágenes y aprobaciones. Confirmamos el calendario antes de comenzar.'],
                    ['¿Sirve para anuncios?', 'Sí. Está pensada para recibir tráfico de campañas y llevarlo a una acción medible, como WhatsApp, formulario o compra.'],
                    ['¿Puedo usarla sin publicidad?', 'Sí. También puede funcionar como la página principal de un servicio específico y recibir visitas desde enlaces, redes o búsquedas.'],
                ],
                'related' => [['¿Necesitas presentar todo tu negocio?', 'Conoce nuestras páginas web para negocios.', 'paginas-web'], ['¿Quieres comparar inversiones?', 'Revisa paquetes y precios.', 'precios']],
                'serviceType' => 'Diseño y desarrollo de landing pages',
            ],
            'paginas-web' => [
                'title' => 'Diseño de Páginas Web para Negocios | XpertSystems',
                'description' => 'Creamos páginas web profesionales para negocios en México: diseño móvil, servicios, WhatsApp, formularios, dominio, hosting, SSL y base SEO.',
                'eyebrow' => 'Páginas web profesionales para negocios',
                'h1' => 'Una página web que explica tu negocio y facilita que te contacten',
                'lead' => 'Haz que tus clientes encuentren en un solo lugar tus servicios, experiencia, ubicación y formas de contacto, con una imagen profesional que genere confianza.',
                'primaryCta' => 'Quiero mi página web',
                'introTitle' => 'Tu negocio merece un espacio propio en internet',
                'intro' => 'Las redes sociales ayudan a conversar, pero no sustituyen un sitio que ordena tu información y trabaja todos los días. Construimos páginas empresariales para que una persona entienda qué haces, por qué elegirte y cómo dar el siguiente paso.',
                'benefits' => [
                    ['Genera confianza', 'Presenta tu marca, servicios, proyectos y datos reales con una experiencia consistente.'],
                    ['Ayuda a que te encuentren', 'Creamos una base rastreable, rápida y organizada para que Google comprenda tu negocio.'],
                    ['Recibe solicitudes', 'Integramos WhatsApp, formularios, correo, teléfono y mapa cuando la información está disponible.'],
                    ['Se adapta a tu crecimiento', 'La estructura permite ampliar servicios o contenido sin rehacer toda tu presencia digital.'],
                ],
                'includes' => ['Hasta cinco secciones o páginas principales según el paquete', 'Diseño personalizado y adaptable a celular', 'Presentación clara de servicios y beneficios', 'WhatsApp, formulario y redes sociales', 'Mapa de ubicación cuando corresponda', 'Dominio .com, hosting, certificado SSL y soporte técnico durante el primer año'],
                'process' => [['Conocemos tu negocio', 'Reunimos servicios, clientes ideales, diferenciadores y objetivos.'], ['Diseñamos la estructura', 'Definimos el recorrido desde la primera impresión hasta el contacto.'], ['Construimos y revisamos', 'Desarrollamos el sitio y afinamos contigo los elementos incluidos.'], ['Publicamos', 'Configuramos dominio, hosting, SSL y la base técnica para buscadores.']],
                'faqs' => [
                    ['¿Necesito una página si ya tengo redes sociales?', 'Sí, cuando buscas un espacio propio que concentre información, aparezca en búsquedas y no dependa del formato o alcance de una plataforma.'],
                    ['¿Mi página aparecerá en Google?', 'La entregamos con una base técnica indexable. La posición depende también de competencia, autoridad, contenido, ubicación y trabajo continuo; nadie puede garantizar el primer lugar.'],
                    ['¿Funcionará en celular?', 'Sí. Diseñamos y verificamos la experiencia para teléfonos, tabletas y computadoras.'],
                    ['¿Incluye dominio y seguridad?', 'Los paquetes publicados incluyen dominio .com, hosting y certificado SSL durante el primer año, según sus condiciones.'],
                ],
                'related' => [['¿Solo promoverás una oferta?', 'Una landing page puede ser más conveniente.', 'landing-pages'], ['¿Vendes productos?', 'Conoce las tiendas en línea.', 'tiendas-en-linea']],
                'serviceType' => 'Diseño y desarrollo de páginas web profesionales',
            ],
            'tiendas-en-linea' => [
                'title' => 'Tiendas en Línea para Negocios en México | XpertSystems',
                'description' => 'Creamos tiendas en línea administrables para vender productos en México, con catálogo, carrito, pagos, envíos, capacitación y diseño móvil.',
                'eyebrow' => 'Comercio electrónico para negocios',
                'h1' => 'Una tienda en línea preparada para recibir pedidos y crecer contigo',
                'lead' => 'Muestra tus productos, cobra en línea y administra tu catálogo desde un sitio diseñado para que comprar sea claro desde el celular.',
                'primaryCta' => 'Cotizar mi tienda',
                'introTitle' => 'Vende sin depender solo de mensajes directos',
                'intro' => 'Una tienda en línea ordena productos, precios, variantes y pedidos para que tus clientes puedan avanzar sin esperar una respuesta manual. Antes de cotizar revisamos catálogo, pagos, envíos e integraciones para recomendar una solución realista.',
                'benefits' => [
                    ['Catálogo administrable', 'Después de la capacitación puedes actualizar productos y continuar creciendo tu inventario.'],
                    ['Compra clara en celular', 'Diseñamos categorías, fichas, carrito y checkout para reducir fricción.'],
                    ['Pagos en línea', 'Configuramos inicialmente un método compatible con el alcance acordado y tu operación.'],
                    ['Alcance transparente', 'Definimos carga inicial, variantes, envíos e integraciones antes de fijar el precio final.'],
                ],
                'includes' => ['Diseño personalizado y responsive', 'Catálogo, carrito y checkout', 'Configuración inicial de pagos y envíos', 'Carga inicial de productos acordada', 'Capacitación básica para administrar el catálogo', 'Dominio .com, hosting, SSL y soporte técnico durante el primer año'],
                'process' => [['Revisamos tu operación', 'Conocemos productos, variantes, inventario, pagos y zonas de envío.'], ['Definimos el alcance', 'Acordamos plataforma, carga inicial e integraciones antes de cotizar.'], ['Construimos la tienda', 'Diseñamos catálogo, fichas, carrito y proceso de compra.'], ['Capacitamos y publicamos', 'Probamos el recorrido y te mostramos cómo administrar productos.']],
                'faqs' => [
                    ['¿Cuánto cuesta una tienda en línea?', 'El precio publicado parte de $5,900 MXN. La cifra final depende de productos, variantes, pagos, envíos e integraciones.'],
                    ['¿Podré agregar productos?', 'Sí. Incluimos capacitación básica para que puedas administrar el catálogo después de la entrega.'],
                    ['¿Incluye pasarela de pago?', 'Incluye la configuración inicial de un método de pago dentro del alcance acordado. Las comisiones del proveedor son externas.'],
                    ['¿Pueden migrar mi catálogo?', 'Podemos revisar formato, cantidad y calidad de los datos para cotizar la migración adecuada.'],
                ],
                'related' => [['¿Vendes un solo servicio?', 'Evalúa una landing page enfocada.', 'landing-pages'], ['¿Quieres ver la inversión inicial?', 'Consulta paquetes y condiciones.', 'precios']],
                'serviceType' => 'Diseño y desarrollo de tiendas en línea',
            ],
            'paginas-web-merida' => [
                'title' => 'Diseño de Páginas Web en Mérida, Yucatán | XpertSystems',
                'description' => 'Diseño de páginas web en Mérida para negocios que quieren generar confianza y recibir contactos. Atención cercana y servicio disponible en todo México.',
                'eyebrow' => 'Diseño web en Mérida, Yucatán',
                'h1' => 'Páginas web para negocios de Mérida que quieren atraer más clientes',
                'lead' => 'Creamos sitios claros, rápidos y fáciles de contactar para empresas y emprendedores en Mérida, con atención remota disponible en todo México.',
                'primaryCta' => 'Cotizar mi proyecto en Mérida',
                'introTitle' => 'Una presencia local que inspira confianza',
                'intro' => 'Cuando alguien busca un servicio en Mérida, tu página debe explicar con rapidez qué haces, dónde atiendes y cómo contactarte. Integramos la ubicación de forma natural y usamos únicamente los datos reales que nos proporciones.',
                'benefits' => [
                    ['Contexto local', 'Organizamos servicios, zonas de atención y datos de contacto para búsquedas relevantes en Mérida.'],
                    ['Contacto inmediato', 'WhatsApp, teléfono, correo y mapa pueden estar a un toque cuando esos datos estén disponibles.'],
                    ['Experiencia móvil', 'Priorizamos a quien busca desde su teléfono mientras compara opciones locales.'],
                    ['Alcance nacional', 'Tu sitio puede atender a Mérida como mercado principal sin cerrarte oportunidades en otras ciudades.'],
                ],
                'includes' => ['Arquitectura según servicios reales', 'Contenido con referencias locales naturales', 'Diseño adaptable a celular', 'Integración de WhatsApp y datos de contacto', 'Base técnica para Google Search Console', 'Dominio, hosting y SSL según el paquete elegido'],
                'process' => [['Definimos el mercado', 'Aclaramos servicios, cliente ideal y zonas reales de atención.'], ['Organizamos la información', 'Creamos una estructura útil para personas y buscadores.'], ['Diseñamos el contacto', 'Hacemos visibles ubicación, WhatsApp y acciones importantes.'], ['Publicamos y verificamos', 'Dejamos el sitio listo para Search Console y seguimiento de conversiones.']],
                'faqs' => [
                    ['¿Solo trabajan con negocios de Mérida?', 'No. Mérida es nuestro mercado local principal, pero podemos trabajar de forma remota con clientes de cualquier ciudad.'],
                    ['¿Incluyen Google Maps?', 'Sí, cuando cuentas con una ubicación pública real y conviene mostrarla. No inventamos direcciones ni fichas de negocio.'],
                    ['¿Me ayudan a aparecer en Google?', 'Creamos la base técnica y local del sitio. También te indicamos acciones externas como Search Console y Perfil de Empresa en Google.'],
                    ['¿La atención es presencial?', 'La atención puede realizarse de forma remota. Consulta por WhatsApp la disponibilidad para tu proyecto.'],
                ],
                'related' => [['Conoce el servicio completo', 'Revisa páginas web para negocios.', 'paginas-web'], ['¿Ya sabes qué necesitas?', 'Compara precios y paquetes.', 'precios']],
                'serviceType' => 'Diseño de páginas web en Mérida, Yucatán',
            ],
            'portafolio' => [
                'title' => 'Portafolio de Diseño y Desarrollo Web | XpertSystems',
                'description' => 'Conoce proyectos de páginas web creados por XpertSystems y descubre cómo usamos claridad, identidad y experiencia móvil para apoyar a cada negocio.',
                'eyebrow' => 'Proyectos realizados',
                'h1' => 'Páginas web creadas con una idea comercial clara',
                'lead' => 'Explora una selección de trabajos y mira cómo adaptamos la estructura, el mensaje y la experiencia visual a cada proyecto.',
                'primaryCta' => 'Quiero un sitio para mi negocio',
                'introTitle' => 'El diseño debe servir al objetivo del negocio',
                'intro' => 'No partimos de una plantilla de mensajes genéricos. Cada proyecto busca que la marca se entienda, se sienta confiable y facilite una acción concreta.',
                'benefits' => [['Estructura con intención', 'Cada sección responde a una pregunta real del cliente potencial.'], ['Diseño adaptable', 'La identidad se mantiene clara en celular y computadora.'], ['Contacto visible', 'Las llamadas a la acción acompañan el recorrido sin saturarlo.'], ['Base técnica', 'El sitio se entrega con fundamentos de rendimiento, seguridad y rastreo.']],
                'includes' => [], 'process' => [],
                'faqs' => [['¿Todos los proyectos cuestan lo mismo?', 'No. El precio depende del alcance, contenido, integraciones y tipo de sitio.'], ['¿Pueden seguir mi identidad de marca?', 'Sí. Revisamos los materiales disponibles y definimos una dirección coherente con tu negocio.']],
                'related' => [['¿Buscas una página empresarial?', 'Conoce el servicio de páginas web.', 'paginas-web'], ['¿Quieres una campaña enfocada?', 'Conoce nuestras landing pages.', 'landing-pages']],
                'serviceType' => 'Diseño web y desarrollo web',
            ],
            'precios' => [
                'title' => 'Precios de Páginas Web y Landing Pages | XpertSystems',
                'description' => 'Compara precios de landing pages, páginas profesionales y tiendas en línea. Conoce qué incluye cada paquete y la renovación anual desde el inicio.',
                'eyebrow' => 'Precios claros para empezar',
                'h1' => 'Elige una página web según la meta de tu negocio',
                'lead' => 'Compara la inversión inicial, lo que incluye cada opción y las condiciones de renovación antes de contratar o solicitar una cotización.',
                'primaryCta' => 'Ayúdame a elegir',
                'introTitle' => 'Una inversión definida, sin ocultar lo esencial',
                'intro' => 'Una landing page funciona para una oferta; una página profesional presenta el negocio completo; una tienda agrega catálogo y operación de venta. Si tu proyecto requiere algo distinto, revisamos el alcance contigo.',
                'benefits' => [['Landing Page', 'Desde $2,700 MXN para promocionar una oferta y captar contactos.'], ['Página Profesional', '$4,400 MXN para presentar un negocio, servicios y formas de contacto.'], ['Tienda en Línea', 'Desde $5,900 MXN; se cotiza según catálogo, pagos, envíos e integraciones.'], ['Primer año cubierto', 'Los paquetes indican dominio, hosting, SSL y soporte incluidos durante el primer año.']],
                'includes' => [], 'process' => [],
                'faqs' => [['¿El precio incluye IVA?', 'Confirma las condiciones fiscales aplicables antes de contratar; la información definitiva se muestra durante el proceso comercial.'], ['¿Hay pagos mensuales?', 'Los paquetes estándar publicados se manejan mediante un pago único inicial y una renovación anual separada.'], ['¿Qué ocurre después del primer año?', 'Puedes renovar dominio, hosting, SSL y soporte con la tarifa indicada en cada paquete.']],
                'related' => [['¿Quieres captar prospectos?', 'Compara el servicio de landing pages.', 'landing-pages'], ['¿Necesitas vender productos?', 'Revisa tiendas en línea.', 'tiendas-en-linea']],
                'serviceType' => 'Paquetes de diseño y desarrollo web',
            ],
            'contacto' => [
                'title' => 'Cotiza tu Página Web | Contacto XpertSystems',
                'description' => 'Cuéntanos qué necesita tu negocio y recibe orientación para elegir una landing page, página profesional o tienda en línea. Atención por WhatsApp.',
                'eyebrow' => 'Hablemos de tu proyecto',
                'h1' => 'Cuéntanos qué quieres lograr con tu página web',
                'lead' => 'Dinos qué vende tu negocio, quién es tu cliente y qué acción quieres recibir. Te ayudamos a elegir una opción sin llenarte de tecnicismos.',
                'primaryCta' => 'Hablar por WhatsApp',
                'introTitle' => 'Empieza con tres datos sencillos',
                'intro' => 'Para orientarte mejor, cuéntanos a qué se dedica tu negocio, si ya tienes página o dominio y qué te gustaría que hicieran tus visitantes: escribirte, solicitar una cotización o comprar.',
                'benefits' => [['Respuesta personal', 'Revisamos tu caso y te ayudamos a elegir el alcance adecuado.'], ['Sin compromiso', 'Puedes resolver dudas antes de contratar.'], ['Opciones claras', 'Te explicamos qué incluye el paquete y qué requeriría una cotización adicional.'], ['Atención remota', 'Trabajamos con negocios de Mérida y de cualquier ciudad de México.']],
                'includes' => [], 'process' => [],
                'faqs' => [['¿Qué información debo enviar?', 'El giro de tu negocio, el objetivo del sitio y cualquier referencia o material que ya tengas.'], ['¿Puedo escribir si aún no sé qué paquete necesito?', 'Sí. Esa es precisamente una de las razones para contactarnos.']],
                'related' => [['¿Quieres comparar primero?', 'Consulta precios y paquetes.', 'precios'], ['¿Buscas ejemplos?', 'Revisa nuestro portafolio.', 'portafolio']],
                'serviceType' => 'Cotización de diseño y desarrollo web',
            ],
        ];
    }
}
