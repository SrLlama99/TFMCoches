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

/* bits/header.html.twig */
class __TwigTemplate_c653789db3c966f29aa7c4d78a8d77fa extends Template
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
        ];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        $__internal_5a27a8ba21ca79b61932376b2fa922d2 = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->enter($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "bits/header.html.twig"));

        $__internal_6f47bbe9983af81f1e7450e9a3e3768f = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->enter($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "bits/header.html.twig"));

        // line 1
        yield "<nav class=\"customnav navbar navbar-expand-md m-1 p-2\" data-bs-theme=\"dark\">
    <div class=\"container\">
        <span class=\"navbar-brand\">
            <img class=\"navBarLogo\" draggable=\"false\" src=";
        // line 4
        yield $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Symfony\Bridge\Twig\Extension\AssetExtension']->getAssetUrl("logo.png"), "html", null, true);
        yield " />
        </span>
        <button class=\"navbar-toggler\" data-bs-toggle=\"collapse\" data-bs-target=\"#navbarCollapse\">
            <span class=\"navbar-toggler-icon\"></span>
        </button>
        <div id=\"navbarCollapse\" class=\"collapse navbar-collapse\">
           <ul class=\"navbar-nav gap-3 w-100 flex-row justify-content-center my-2 my-md-0\">
            <li class=\"nav-item\">
               <form class=\"navForm\" method=\"get\" action=";
        // line 12
        yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("home");
        yield "> ";
        // line 13
        yield "                    <input type=\"text\" name=\"q\" class=\"navSearchBar form-control\"/>
                    <button type=\"submit\" class=\"searchButton customNavBtn btn\">
                        <i class=\"bi bi-search\"></i>
                    </button>
               </form>
            </li>

            ";
        // line 20
        if ((($tmp =  !CoreExtension::getAttribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 20, $this->source); })()), "user", [], "any", false, false, false, 20)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            yield " ";
            // line 21
            yield "            <li class=\"nav-item ms-md-auto\">
                <a class=\"nav-link logIOButton customNavBtn btn\" title=\"Log in\" href=";
            // line 22
            yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("home");
            yield "> ";
            // line 23
            yield "                    <i class=\"bi bi-box-arrow-in-right\"></i>
                    Login
                </a>
            </li>
            ";
        } else {
            // line 27
            yield " ";
            // line 28
            yield "            <li class=\"nav-item ms-md-auto\">
                <a class=\"garageButton customNavBtn btn\" title=\"My garage\">
                    <i class=\"bi bi-car-front-fill\"></i>
                    <span class=\"d-none d-md-inline\">Garage</span>
                </a>
            </li>
            <li class=\"nav-item\">
                <a class=\"logIOButton customNavBtn btn\" title=\"Log out\" href=";
            // line 35
            yield $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("home");
            yield "> ";
            // line 36
            yield "                    <i class=\"bi bi-box-arrow-right\"></i>
                </a>
            </li>
            ";
        }
        // line 40
        yield "            </ul>
        </div>
    </div>
</nav>";
        
        $__internal_5a27a8ba21ca79b61932376b2fa922d2->leave($__internal_5a27a8ba21ca79b61932376b2fa922d2_prof);

        
        $__internal_6f47bbe9983af81f1e7450e9a3e3768f->leave($__internal_6f47bbe9983af81f1e7450e9a3e3768f_prof);

        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "bits/header.html.twig";
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
        return array (  112 => 40,  106 => 36,  103 => 35,  94 => 28,  92 => 27,  85 => 23,  82 => 22,  79 => 21,  76 => 20,  67 => 13,  64 => 12,  53 => 4,  48 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<nav class=\"customnav navbar navbar-expand-md m-1 p-2\" data-bs-theme=\"dark\">
    <div class=\"container\">
        <span class=\"navbar-brand\">
            <img class=\"navBarLogo\" draggable=\"false\" src={{ asset('logo.png') }} />
        </span>
        <button class=\"navbar-toggler\" data-bs-toggle=\"collapse\" data-bs-target=\"#navbarCollapse\">
            <span class=\"navbar-toggler-icon\"></span>
        </button>
        <div id=\"navbarCollapse\" class=\"collapse navbar-collapse\">
           <ul class=\"navbar-nav gap-3 w-100 flex-row justify-content-center my-2 my-md-0\">
            <li class=\"nav-item\">
               <form class=\"navForm\" method=\"get\" action={{ path('home') }}> {# TODO: Change to search path here #}
                    <input type=\"text\" name=\"q\" class=\"navSearchBar form-control\"/>
                    <button type=\"submit\" class=\"searchButton customNavBtn btn\">
                        <i class=\"bi bi-search\"></i>
                    </button>
               </form>
            </li>

            {% if not app.user %} {# ANONYMOUS USER #}
            <li class=\"nav-item ms-md-auto\">
                <a class=\"nav-link logIOButton customNavBtn btn\" title=\"Log in\" href={{ path('home') }}> {# TODO: Change login path #}
                    <i class=\"bi bi-box-arrow-in-right\"></i>
                    Login
                </a>
            </li>
            {% else %} {# USER IS LOGGED IN #}
            <li class=\"nav-item ms-md-auto\">
                <a class=\"garageButton customNavBtn btn\" title=\"My garage\">
                    <i class=\"bi bi-car-front-fill\"></i>
                    <span class=\"d-none d-md-inline\">Garage</span>
                </a>
            </li>
            <li class=\"nav-item\">
                <a class=\"logIOButton customNavBtn btn\" title=\"Log out\" href={{ path('home') }}> {# TODO: Add logout path #}
                    <i class=\"bi bi-box-arrow-right\"></i>
                </a>
            </li>
            {% endif %}
            </ul>
        </div>
    </div>
</nav>", "bits/header.html.twig", "C:\\xampp\\htdocs\\TFM\\TFMCoches\\templates\\bits\\header.html.twig");
    }
}
