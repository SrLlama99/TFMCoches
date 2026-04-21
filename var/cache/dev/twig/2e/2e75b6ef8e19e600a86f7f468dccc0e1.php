<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;
use Twig\TemplateWrapper;

/* skeleton.html.twig */
class __TwigTemplate_d0cd02fb06afe748469a1592e82a80f2 extends Template
{
    private Source $source;
    /**
     * @var array<string, Template>
     */
    private array $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
            'title' => [$this, 'block_title'],
            'stylesheets' => [$this, 'block_stylesheets'],
            'javascripts' => [$this, 'block_javascripts'],
            'importmap' => [$this, 'block_importmap'],
            'body' => [$this, 'block_body'],
        ];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "skeleton.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "skeleton.html.twig"));

        // line 1
        yield "<!DOCTYPE html>
<html >
    <head>
        <meta charset=\"UTF-8\">
        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1, shrink-to-fit=no\">
        <title>";
        // line 6
        yield from $this->unwrap()->yieldBlock('title', $context, $blocks);
        yield " - TFG</title>
        <link rel=\"icon\" href=\"data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 128 128%22><text y=%221.2em%22 font-size=%2296%22>⚫️</text><text y=%221.3em%22 x=%220.2em%22 font-size=%2276%22 fill=%22%23fff%22>sf</text></svg>\">
        
        ";
        // line 10
        yield "        <link href=\"https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css\" rel=\"stylesheet\">
        <link rel=\"stylesheet\" href=\"";
        // line 11
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("/styles/skeleton.css"), "html", null, true);
        yield "\"> </link>

        ";
        // line 13
        yield from $this->unwrap()->yieldBlock('stylesheets', $context, $blocks);
        // line 15
        yield "
        ";
        // line 16
        yield from $this->unwrap()->yieldBlock('javascripts', $context, $blocks);
        // line 19
        yield "
        ";
        // line 21
        yield "        ";
        $context["frankenphpHotReload"] = CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, CoreExtension::getAttribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 21, $this->source); })()), "request", [], "any", false, false, false, 21), "server", [], "any", false, false, false, 21), "get", ["FRANKENPHP_HOT_RELOAD"], "method", false, false, false, 21);
        // line 22
        yield "        ";
        if ((($tmp = (isset($context["frankenphpHotReload"]) || array_key_exists("frankenphpHotReload", $context) ? $context["frankenphpHotReload"] : (function () { throw new RuntimeError('Variable "frankenphpHotReload" does not exist.', 22, $this->source); })())) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 23
            yield "        <meta name=\"frankenphp-hot-reload:url\" content=\"";
            yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape((isset($context["frankenphpHotReload"]) || array_key_exists("frankenphpHotReload", $context) ? $context["frankenphpHotReload"] : (function () { throw new RuntimeError('Variable "frankenphpHotReload" does not exist.', 23, $this->source); })()), "html", null, true);
            yield "\">
        <script src=\"https://cdn.jsdelivr.net/npm/idiomorph\"></script>
        <script src=\"https://cdn.jsdelivr.net/npm/frankenphp-hot-reload/+esm\" type=\"module\"></script>
        ";
        }
        // line 27
        yield "    </head>
    <body class=\"container\">
        ";
        // line 29
        yield from $this->load("bits/header.html.twig", 29)->unwrap()->yield($context);
        // line 30
        yield "        <main class=\"m-2\">
        ";
        // line 31
        yield from $this->unwrap()->yieldBlock('body', $context, $blocks);
        // line 67
        yield "        <main>

        
    </body>
</html>
";
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        yield from [];
    }

    // line 6
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_title(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "title"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "title"));

        yield "⚠CÁMBIAME⚠";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        yield from [];
    }

    // line 13
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_stylesheets(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "stylesheets"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "stylesheets"));

        // line 14
        yield "        ";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        yield from [];
    }

    // line 16
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_javascripts(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "javascripts"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "javascripts"));

        // line 17
        yield "            ";
        yield from $this->unwrap()->yieldBlock('importmap', $context, $blocks);
        // line 18
        yield "        ";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        yield from [];
    }

    // line 17
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_importmap(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "importmap"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "importmap"));

        yield $this->env->getRuntime('Symfony\Bridge\Twig\Extension\ImportMapRuntime')->importmap("app");
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        yield from [];
    }

    // line 31
    /**
     * @return iterable<null|scalar|\Stringable>
     */
    public function block_body(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "body"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "body"));

        // line 32
        yield "        <h1>Nice work dude <span class=\"bi bi-alarm\"></span></h1>
        <p>You should maybe try to override the base body block bro<br>fuck you heres some lorem</p>
        <p>
\t\t\tloremLorem ipsum, dolor sit amet consectetur adipisicing elit. Dolorem rem amet, tempora accusamus quisquam porro nostrum nisi enim dolorum aspernatur veritatis error neque, commodi illo eius perferendis, facere sint obcaecati?
\t\t\tAut corrupti incidunt alias at molestias excepturi ratione aliquid natus? Quis accusantium error et facere voluptate, nihil ut magni, earum saepe adipisci vero perspiciatis est, natus id! Illo, reiciendis voluptatibus?
\t\t\tNobis mollitia, rerum deserunt dicta saepe veritatis, optio aliquid tempore itaque similique reprehenderit pariatur possimus nesciunt delectus nemo illum corrupti libero enim eveniet sit quod iure! Soluta fugit quia similique.
\t\t\tUnde recusandae reprehenderit voluptatum, nemo omnis iure odit rem, illum, harum minima libero vero placeat? Dolores, id, aspernatur non molestias magnam perspiciatis vel error illo, atque iure delectus ipsum necessitatibus.
\t\t\tOmnis iste magnam nesciunt facilis soluta ducimus saepe facere ab, nobis fugiat quo, sapiente ad. Odio temporibus harum iste quia amet fugiat laudantium, suscipit mollitia consectetur voluptate repellat earum blanditiis.
\t\t\tEius dolore culpa laboriosam praesentium fuga debitis quidem labore, quia inventore harum repudiandae architecto consequatur incidunt doloremque accusantium recusandae, libero unde obcaecati tempore velit exercitationem neque nobis. Repudiandae, eligendi ex!
\t\t\tAutem animi asperiores qui quasi perferendis! Inventore recusandae, nesciunt cupiditate repudiandae illum libero magni temporibus cumque, est soluta mollitia quis sunt, nisi sit numquam atque molestias similique. Magnam, repudiandae laboriosam.
\t\t\tPariatur culpa veniam maiores nostrum, itaque consequatur laboriosam repellendus perferendis facilis autem vitae vero excepturi perspiciatis delectus. Reprehenderit culpa eum aperiam beatae ab numquam obcaecati totam sapiente voluptatum, esse aliquam?
\t\t\tAccusantium libero fugit veniam culpa perspiciatis iste totam exercitationem aliquam sapiente ex nulla cupiditate dicta, modi ea unde rem quo quis praesentium magni dolore quasi eligendi ab odit. Temporibus, quae!
\t\t\tOdio veniam debitis qui velit accusamus voluptates dolor tempore fugit vitae eaque obcaecati, nam quam possimus? Velit, molestiae dicta corporis neque atque temporibus laboriosam non quos dignissimos pariatur totam voluptatum?
\t\t\tMagnam praesentium esse porro culpa molestiae itaque, voluptatum eum, soluta voluptas, exercitationem sequi reprehenderit. Facilis rem adipisci eos. Unde placeat ullam saepe vel modi consequatur, ipsum earum. Magnam, tenetur placeat.
\t\t\tEsse officia consequatur quidem temporibus pariatur doloremque quod mollitia voluptate. Eveniet voluptas labore dolorem quasi autem debitis est eaque non laborum beatae molestias perspiciatis magni optio tempora, repellendus ipsa. Similique.
\t\t\tDoloremque omnis nobis facere, recusandae dicta ad similique molestias consequuntur! Odio a doloremque nemo, beatae ipsa aspernatur placeat praesentium laborum, porro recusandae assumenda delectus voluptatum ipsum, rerum optio? A, ab.
\t\t\tIllum iste voluptatum aspernatur amet totam fugit incidunt quasi, assumenda repellat corporis, beatae tenetur architecto ipsam velit optio! Sint beatae ex hic natus ab ipsum necessitatibus sit quod sapiente est!
\t\t\tPraesentium nam dolorem non rem exercitationem. Eum, cupiditate laborum. Sed soluta optio odit, amet pariatur quos exercitationem deleniti vitae expedita nesciunt, vero porro aliquam ipsum minima maiores assumenda temporibus ratione.
\t\t\tEt recusandae inventore minus aperiam repellendus, suscipit blanditiis quae eius in corporis, optio voluptates nobis ea, natus pariatur earum libero! Ad voluptate molestias eaque optio nostrum molestiae, omnis laudantium quo.
\t\t\tNumquam repellat fugiat corrupti dolorum, consectetur consequuntur, illum, animi saepe sunt voluptas asperiores dolores quis id libero beatae obcaecati commodi. Sed soluta rem sunt ad inventore odit! Voluptatem, illum aliquam.
\t\t\tTempora autem nisi eligendi. Quibusdam atque ex soluta earum laudantium nihil! Architecto blanditiis veritatis inventore dolorem assumenda aspernatur accusamus nesciunt exercitationem nobis magni, dignissimos alias, sapiente ab, reiciendis aliquid necessitatibus?
\t\t\tNesciunt voluptatum et rerum pariatur quia explicabo asperiores nobis! Optio, esse! Vero, ullam placeat in similique nesciunt, distinctio accusamus ipsam quae explicabo hic labore perspiciatis corporis corrupti voluptatibus ut nobis.
\t\t\tConsequatur, impedit. Error expedita minus debitis laudantium ad qui animi labore repellat cum accusantium numquam commodi quasi impedit porro reiciendis quos atque doloremque praesentium deserunt, odio perspiciatis at nobis aliquam!
\t\t\tVoluptatem eveniet fugiat fuga, ipsa earum velit ipsam molestias nobis corrupti assumenda odio libero non obcaecati quasi esse repudiandae laudantium officia cupiditate dicta deserunt. Possimus, distinctio alias! Suscipit, incidunt? Maxime.
\t\t\tMagnam saepe atque incidunt molestias perspiciatis voluptatem ullam odio quis accusamus repellat eius ad nisi quo exercitationem fuga, voluptas, cumque, corporis unde aliquid! Impedit assumenda fugiat ab provident pariatur ipsum!
\t\t\tFacere alias voluptatum beatae architecto omnis earum magni vel veritatis soluta rem. Temporibus repellendus voluptates est ut laudantium, aliquid quod illo quisquam quas totam a ab ex laborum nemo dicta?
\t\t\tVeritatis, pariatur saepe dicta assumenda tempora distinctio soluta, inventore dolores impedit iusto nisi et laborum veniam totam necessitatibus sint iure atque quod consequatur quas blanditiis. Consequatur quia quam molestias consectetur?
\t\t\tHarum nam fuga veniam! Commodi aliquid fugit consequuntur fuga at sapiente quod sunt rerum praesentium similique vel magni fugiat ullam earum minus debitis, eos atque sit quam! Reiciendis, mollitia hic.
\t\t\tOptio quam sed eius illo maxime ullam, repellendus minus repudiandae dolor culpa. Illo, repudiandae itaque voluptate autem vero quam, nemo vel cupiditate sint voluptatibus saepe iste ad quia ratione modi.
\t\t\tEos, voluptates neque dolorum at qui enim, error nisi mollitia a veritatis officia cupiditate nulla id incidunt! Perferendis, tenetur vero. Veniam animi natus, totam ducimus doloremque iusto vel iste aliquam.
\t\t\tVeniam optio maiores quisquam itaque quod obcaecati expedita aspernatur error assumenda eaque! Eligendi veniam nobis porro cumque, dolorem soluta libero tenetur, sed illo corporis inventore nulla, vel ex eaque itaque.
\t\t\tLaboriosam hic, in fugiat odio quis odit quisquam, quia dolorem dolorum totam, excepturi iusto placeat aspernatur. Quam voluptas quidem, ipsum, molestiae ad minus blanditiis enim voluptate incidunt ut nobis aliquam.
\t\t\tAut corrupti quis deserunt, explicabo nemo vitae aliquid voluptas nobis facilis sunt eveniet molestias, commodi illum corporis, quos saepe unde hic enim. Dolorem necessitatibus iste at, consectetur fuga cupiditate quam.
\t\t</p>
        ";
        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "skeleton.html.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable(): bool
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  235 => 32,  222 => 31,  199 => 17,  188 => 18,  185 => 17,  172 => 16,  161 => 14,  148 => 13,  125 => 6,  109 => 67,  107 => 31,  104 => 30,  102 => 29,  98 => 27,  90 => 23,  87 => 22,  84 => 21,  81 => 19,  79 => 16,  76 => 15,  74 => 13,  69 => 11,  66 => 10,  60 => 6,  53 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<!DOCTYPE html>
<html >
    <head>
        <meta charset=\"UTF-8\">
        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1, shrink-to-fit=no\">
        <title>{% block title %}⚠CÁMBIAME⚠{% endblock %} - TFG</title>
        <link rel=\"icon\" href=\"data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 128 128%22><text y=%221.2em%22 font-size=%2296%22>⚫️</text><text y=%221.3em%22 x=%220.2em%22 font-size=%2276%22 fill=%22%23fff%22>sf</text></svg>\">
        
        {# Bootstrap icons #}
        <link href=\"https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css\" rel=\"stylesheet\">
        <link rel=\"stylesheet\" href=\"{{ asset(\"/styles/skeleton.css\") }}\"> </link>

        {% block stylesheets %}
        {% endblock %}

        {% block javascripts %}
            {% block importmap %}{{ importmap('app') }}{% endblock %}
        {% endblock %}

        {# Para ver el porqué de este código https://frankenphp.dev/docs/hot-reload/ #}
        {% set frankenphpHotReload = app.request.server.get('FRANKENPHP_HOT_RELOAD') %}
        {% if frankenphpHotReload %}
        <meta name=\"frankenphp-hot-reload:url\" content=\"{{ frankenphpHotReload }}\">
        <script src=\"https://cdn.jsdelivr.net/npm/idiomorph\"></script>
        <script src=\"https://cdn.jsdelivr.net/npm/frankenphp-hot-reload/+esm\" type=\"module\"></script>
        {% endif %}
    </head>
    <body class=\"container\">
        {% include \"bits/header.html.twig\" %}
        <main class=\"m-2\">
        {% block body %}
        <h1>Nice work dude <span class=\"bi bi-alarm\"></span></h1>
        <p>You should maybe try to override the base body block bro<br>fuck you heres some lorem</p>
        <p>
\t\t\tloremLorem ipsum, dolor sit amet consectetur adipisicing elit. Dolorem rem amet, tempora accusamus quisquam porro nostrum nisi enim dolorum aspernatur veritatis error neque, commodi illo eius perferendis, facere sint obcaecati?
\t\t\tAut corrupti incidunt alias at molestias excepturi ratione aliquid natus? Quis accusantium error et facere voluptate, nihil ut magni, earum saepe adipisci vero perspiciatis est, natus id! Illo, reiciendis voluptatibus?
\t\t\tNobis mollitia, rerum deserunt dicta saepe veritatis, optio aliquid tempore itaque similique reprehenderit pariatur possimus nesciunt delectus nemo illum corrupti libero enim eveniet sit quod iure! Soluta fugit quia similique.
\t\t\tUnde recusandae reprehenderit voluptatum, nemo omnis iure odit rem, illum, harum minima libero vero placeat? Dolores, id, aspernatur non molestias magnam perspiciatis vel error illo, atque iure delectus ipsum necessitatibus.
\t\t\tOmnis iste magnam nesciunt facilis soluta ducimus saepe facere ab, nobis fugiat quo, sapiente ad. Odio temporibus harum iste quia amet fugiat laudantium, suscipit mollitia consectetur voluptate repellat earum blanditiis.
\t\t\tEius dolore culpa laboriosam praesentium fuga debitis quidem labore, quia inventore harum repudiandae architecto consequatur incidunt doloremque accusantium recusandae, libero unde obcaecati tempore velit exercitationem neque nobis. Repudiandae, eligendi ex!
\t\t\tAutem animi asperiores qui quasi perferendis! Inventore recusandae, nesciunt cupiditate repudiandae illum libero magni temporibus cumque, est soluta mollitia quis sunt, nisi sit numquam atque molestias similique. Magnam, repudiandae laboriosam.
\t\t\tPariatur culpa veniam maiores nostrum, itaque consequatur laboriosam repellendus perferendis facilis autem vitae vero excepturi perspiciatis delectus. Reprehenderit culpa eum aperiam beatae ab numquam obcaecati totam sapiente voluptatum, esse aliquam?
\t\t\tAccusantium libero fugit veniam culpa perspiciatis iste totam exercitationem aliquam sapiente ex nulla cupiditate dicta, modi ea unde rem quo quis praesentium magni dolore quasi eligendi ab odit. Temporibus, quae!
\t\t\tOdio veniam debitis qui velit accusamus voluptates dolor tempore fugit vitae eaque obcaecati, nam quam possimus? Velit, molestiae dicta corporis neque atque temporibus laboriosam non quos dignissimos pariatur totam voluptatum?
\t\t\tMagnam praesentium esse porro culpa molestiae itaque, voluptatum eum, soluta voluptas, exercitationem sequi reprehenderit. Facilis rem adipisci eos. Unde placeat ullam saepe vel modi consequatur, ipsum earum. Magnam, tenetur placeat.
\t\t\tEsse officia consequatur quidem temporibus pariatur doloremque quod mollitia voluptate. Eveniet voluptas labore dolorem quasi autem debitis est eaque non laborum beatae molestias perspiciatis magni optio tempora, repellendus ipsa. Similique.
\t\t\tDoloremque omnis nobis facere, recusandae dicta ad similique molestias consequuntur! Odio a doloremque nemo, beatae ipsa aspernatur placeat praesentium laborum, porro recusandae assumenda delectus voluptatum ipsum, rerum optio? A, ab.
\t\t\tIllum iste voluptatum aspernatur amet totam fugit incidunt quasi, assumenda repellat corporis, beatae tenetur architecto ipsam velit optio! Sint beatae ex hic natus ab ipsum necessitatibus sit quod sapiente est!
\t\t\tPraesentium nam dolorem non rem exercitationem. Eum, cupiditate laborum. Sed soluta optio odit, amet pariatur quos exercitationem deleniti vitae expedita nesciunt, vero porro aliquam ipsum minima maiores assumenda temporibus ratione.
\t\t\tEt recusandae inventore minus aperiam repellendus, suscipit blanditiis quae eius in corporis, optio voluptates nobis ea, natus pariatur earum libero! Ad voluptate molestias eaque optio nostrum molestiae, omnis laudantium quo.
\t\t\tNumquam repellat fugiat corrupti dolorum, consectetur consequuntur, illum, animi saepe sunt voluptas asperiores dolores quis id libero beatae obcaecati commodi. Sed soluta rem sunt ad inventore odit! Voluptatem, illum aliquam.
\t\t\tTempora autem nisi eligendi. Quibusdam atque ex soluta earum laudantium nihil! Architecto blanditiis veritatis inventore dolorem assumenda aspernatur accusamus nesciunt exercitationem nobis magni, dignissimos alias, sapiente ab, reiciendis aliquid necessitatibus?
\t\t\tNesciunt voluptatum et rerum pariatur quia explicabo asperiores nobis! Optio, esse! Vero, ullam placeat in similique nesciunt, distinctio accusamus ipsam quae explicabo hic labore perspiciatis corporis corrupti voluptatibus ut nobis.
\t\t\tConsequatur, impedit. Error expedita minus debitis laudantium ad qui animi labore repellat cum accusantium numquam commodi quasi impedit porro reiciendis quos atque doloremque praesentium deserunt, odio perspiciatis at nobis aliquam!
\t\t\tVoluptatem eveniet fugiat fuga, ipsa earum velit ipsam molestias nobis corrupti assumenda odio libero non obcaecati quasi esse repudiandae laudantium officia cupiditate dicta deserunt. Possimus, distinctio alias! Suscipit, incidunt? Maxime.
\t\t\tMagnam saepe atque incidunt molestias perspiciatis voluptatem ullam odio quis accusamus repellat eius ad nisi quo exercitationem fuga, voluptas, cumque, corporis unde aliquid! Impedit assumenda fugiat ab provident pariatur ipsum!
\t\t\tFacere alias voluptatum beatae architecto omnis earum magni vel veritatis soluta rem. Temporibus repellendus voluptates est ut laudantium, aliquid quod illo quisquam quas totam a ab ex laborum nemo dicta?
\t\t\tVeritatis, pariatur saepe dicta assumenda tempora distinctio soluta, inventore dolores impedit iusto nisi et laborum veniam totam necessitatibus sint iure atque quod consequatur quas blanditiis. Consequatur quia quam molestias consectetur?
\t\t\tHarum nam fuga veniam! Commodi aliquid fugit consequuntur fuga at sapiente quod sunt rerum praesentium similique vel magni fugiat ullam earum minus debitis, eos atque sit quam! Reiciendis, mollitia hic.
\t\t\tOptio quam sed eius illo maxime ullam, repellendus minus repudiandae dolor culpa. Illo, repudiandae itaque voluptate autem vero quam, nemo vel cupiditate sint voluptatibus saepe iste ad quia ratione modi.
\t\t\tEos, voluptates neque dolorum at qui enim, error nisi mollitia a veritatis officia cupiditate nulla id incidunt! Perferendis, tenetur vero. Veniam animi natus, totam ducimus doloremque iusto vel iste aliquam.
\t\t\tVeniam optio maiores quisquam itaque quod obcaecati expedita aspernatur error assumenda eaque! Eligendi veniam nobis porro cumque, dolorem soluta libero tenetur, sed illo corporis inventore nulla, vel ex eaque itaque.
\t\t\tLaboriosam hic, in fugiat odio quis odit quisquam, quia dolorem dolorum totam, excepturi iusto placeat aspernatur. Quam voluptas quidem, ipsum, molestiae ad minus blanditiis enim voluptate incidunt ut nobis aliquam.
\t\t\tAut corrupti quis deserunt, explicabo nemo vitae aliquid voluptas nobis facilis sunt eveniet molestias, commodi illum corporis, quos saepe unde hic enim. Dolorem necessitatibus iste at, consectetur fuga cupiditate quam.
\t\t</p>
        {% endblock %}
        <main>

        
    </body>
</html>
", "skeleton.html.twig", "C:\\xampp\\htdocs\\TFM\\TFMCoches\\templates\\skeleton.html.twig");
    }
}
