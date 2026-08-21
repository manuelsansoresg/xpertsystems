# Auditoría e implementación SEO — XpertSystems

Fecha: 21 de agosto de 2026

## Diagnóstico inicial

- Solo la home y las páginas legales tenían URLs públicas indexables; los servicios competían dentro de una sola página sin una URL propia.
- La navegación principal usaba anclas internas, por lo que no distribuía autoridad hacia páginas comerciales especializadas.
- El sitemap incluía home y páginas legales, pero omitía todas las intenciones comerciales solicitadas.
- `robots.txt` solo bloqueaba webhooks y no protegía paneles, checkout ni resultados de pago.
- Checkout, estados de pago y páginas legales no tenían una directiva explícita de no indexación.
- La home tenía canonical, Open Graph y Twitter Cards, pero un title genérico y datos estructurados mínimos.
- Las imágenes del portafolio sumaban aproximadamente 6.8 MB y la imagen principal pesaba 1.3 MB.
- La medición admitía GA4 mediante variable de entorno, pero no normalizaba el clic en WhatsApp ni medía envíos de formularios.
- No existían páginas específicas para landing pages, páginas web, tiendas en línea, precios, portafolio, contacto o Mérida.

## Arquitectura e intención asignada

| Página | Keyword principal | Keywords secundarias | Title | H1 | Meta description | Intención |
|---|---|---|---|---|---|---|
| `/` | páginas web para negocios | diseño web, desarrollo web en México, sitios web profesionales | Páginas Web para Negocios en México \| XpertSystems | Páginas web que ayudan a tu negocio a conseguir clientes. | Creamos páginas web, landing pages y tiendas en línea para negocios que quieren generar confianza, atraer prospectos y recibir contactos en México. | Comercial general |
| `/landing-pages` | landing pages | diseño landing page, landing page para negocio, landing con WhatsApp | Landing Pages para Conseguir Clientes \| XpertSystems | Convierte tus visitas en prospectos con una landing page clara | Diseñamos landing pages rápidas y claras para convertir campañas en prospectos. Incluye WhatsApp, formulario, dominio, hosting y SSL por un año. | Contratación específica |
| `/paginas-web` | diseño de páginas web para negocios | página empresarial, sitio web profesional, páginas web con WhatsApp | Diseño de Páginas Web para Negocios \| XpertSystems | Una página web que explica tu negocio y facilita que te contacten | Creamos páginas web profesionales para negocios en México: diseño móvil, servicios, WhatsApp, formularios, dominio, hosting, SSL y base SEO. | Contratación específica |
| `/tiendas-en-linea` | tiendas en línea para negocios | tienda online, catálogo administrable, ecommerce México | Tiendas en Línea para Negocios en México \| XpertSystems | Una tienda en línea preparada para recibir pedidos y crecer contigo | Creamos tiendas en línea administrables para vender productos en México, con catálogo, carrito, pagos, envíos, capacitación y diseño móvil. | Contratación específica |
| `/paginas-web-merida` | diseño de páginas web en Mérida | desarrollador web Mérida, páginas web Yucatán, desarrollo web Mérida | Diseño de Páginas Web en Mérida, Yucatán \| XpertSystems | Páginas web para negocios de Mérida que quieren atraer más clientes | Diseño de páginas web en Mérida para negocios que quieren generar confianza y recibir contactos. Atención cercana y servicio disponible en todo México. | Comercial local |
| `/portafolio` | portafolio de diseño web | proyectos web, trabajos realizados, ejemplos páginas web | Portafolio de Diseño y Desarrollo Web \| XpertSystems | Páginas web creadas con una idea comercial clara | Conoce proyectos de páginas web creados por XpertSystems y descubre cómo usamos claridad, identidad y experiencia móvil para apoyar a cada negocio. | Evaluación y confianza |
| `/precios` | precios de páginas web | cuánto cuesta una landing page, precio página web México, paquetes web | Precios de Páginas Web y Landing Pages \| XpertSystems | Elige una página web según la meta de tu negocio | Compara precios de landing pages, páginas profesionales y tiendas en línea. Conoce qué incluye cada paquete y la renovación anual desde el inicio. | Comparación y compra |
| `/contacto` | cotizar página web | solicitar información, contratar diseño web, WhatsApp diseño web | Cotiza tu Página Web \| Contacto XpertSystems | Cuéntanos qué quieres lograr con tu página web | Cuéntanos qué necesita tu negocio y recibe orientación para elegir una landing page, página profesional o tienda en línea. Atención por WhatsApp. | Conversión |

`/paginas-web-profesionales` redirige permanentemente a `/paginas-web`; no se crean dos contenidos casi idénticos para la misma intención.

## Mejoras implementadas

- Siete páginas comerciales nuevas con contenido, encabezados, beneficios, objeciones, proceso, preguntas frecuentes y CTA propios.
- Titles, descriptions, H1 y canonical únicos.
- Datos estructurados `Organization`, `ProfessionalService`, `WebSite`, `Service` y `BreadcrumbList` usando solo datos disponibles.
- Sitemap limitado a URLs canónicas, comerciales e indexables.
- Robots dinámico basado en la URL configurada del entorno; bloquea paneles y recorridos transaccionales.
- `noindex` en checkout, estados de pago y páginas legales.
- Enlazado interno desde home, cabeceras, pies, contenidos relacionados y páginas de servicio.
- Nuevos formatos WebP para hero y portafolio, dimensiones explícitas, `loading` y `decoding` apropiados.
- Eventos preparados para `click_whatsapp`, `click_email`, `form_submit`, `quote_request`, `begin_checkout`, `view_packages` y `purchase`.
- Mejoras de foco visible, migas de pan, navegación consistente, objetivos táctiles, estados de error y respeto a movimiento reducido.

## Contenidos recomendados

Prioridad alta:

1. ¿Cuánto cuesta una página web en México en 2026?
2. ¿Cuánto cuesta una landing page y qué debe incluir?
3. Landing page vs. página web: cuál necesita tu negocio.
4. Página web vs. redes sociales para un negocio local.
5. Qué debe tener una página web para conseguir clientes.
6. Cómo hacer que mi negocio aparezca en Google.
7. ¿Necesito una página web si ya vendo por Facebook o Instagram?
8. Errores que hacen que una página web no genere contactos.
9. Cómo recibir solicitudes de cotización desde Google.
10. Cuánto cuesta mantener una página web después del primer año.

Prioridad media:

11. Landing page con WhatsApp: estructura y buenas prácticas.
12. Qué información enviar a un diseñador web antes de comenzar.
13. Tienda en línea o catálogo por WhatsApp: cuándo conviene cada uno.
14. Cómo generar confianza en internet si tu negocio es nuevo.
15. SEO local para negocios de Mérida: página web y Perfil de Empresa.

Cada contenido debe enlazar a un servicio, precios, portafolio y contacto cuando resulte natural. No conviene publicar todos a la vez ni crear páginas equivalentes cambiando únicamente la ciudad.

## Acciones externas pendientes

- Cambiar `APP_URL` por el dominio HTTPS definitivo antes de publicar; canonicals, sitemap, robots y JSON-LD lo usan como fuente.
- Definir `WHATSAPP_NUMBER` y `CONTACT_EMAIL` reales; no se inventaron datos ausentes.
- Añadir `GA4_MEASUREMENT_ID` para activar GA4 y marcar como conversiones los eventos comerciales.
- Verificar el dominio en Google Search Console y enviar `/sitemap.xml`.
- Crear o completar el Perfil de Empresa en Google con nombre, categoría, teléfono, URL, horario y ubicación/área de servicio reales.
- Validar páginas públicas con Rich Results Test y medir Core Web Vitals con datos de campo después del despliegue.
- Conectar Google Tag Manager o Consent Mode si la estrategia legal/analítica lo requiere.
- Conseguir menciones y enlaces legítimos mediante alianzas, directorios empresariales de calidad y proyectos reales; no comprar enlaces.
