<div class="admin-front-banner" style="background: #f0f0f0; padding: 15px; border: 1px solid #ddd; margin-bottom: 20px;">
    <ul style="list-style: none; padding-left: 0; display: flex; gap: 30px; justify-content: center;">
      <li>
        <strong>Produits actifs :</strong>
        {$nb_products|escape:'html':'UTF-8'}
      </li>
      <li>
        <strong>Panier moyen :</strong>
        {$avg_cart|escape:'html':'UTF-8'}
      </li>
      <li>
        <strong>Produit le plus commandé :</strong>
        <a href="{$product_link|escape:'html':'UTF-8'}" style="text-decoration: underline;">
          {$product_name|escape:'html':'UTF-8'}
        </a>
      </li>
    </ul>
  </div>
