{% extends "layout/base.html.twig" %}

{% block title %}Ma Wishlist{% endblock %}

{% block content %}
<h1>Mes offres en wishlist</h1>

{% if offres is empty %}
<p>Aucune offre enregistrée pour le moment.</p>
{% else %}
<ul>
    {% for offre in offres %}
    <li>
        <strong>{{ offre.Nom_Offre }}</strong> – {{ offre.Description_Offre }}
        <a href="?uri=retirer_wishlist&id={{ offre.Id_Offre }}">Retirer</a>
    </li>
    {% endfor %}
</ul>

<!-- Pagination -->
<nav class="pagination">
    {% if page > 1 %}
    <a href="?uri=wishlist&page={{ page - 1 }}">« Précédent</a>
    {% endif %}
    <span>Page {{ page }}</span>
    {% if has_next %}
    <a href="?uri=wishlist&page={{ page + 1 }}">Suivant »</a>
    {% endif %}
</nav>
{% endif %}
{% endblock %}
