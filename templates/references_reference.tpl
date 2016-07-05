<!-- Created by Instant Zero (http://www.instant-zero.com) -->
<{$breadcrumb}>

<h2><{$article.article_title}><{if trim($article.article_date) != ''}> <i>(<{$article.article_date}>)</i><{/if}></h2>
<div class="element">
    <{if $article.article_attachment_exists}>
        <a href="<{$article.article_attachment_url}>" target="_blank"><img src="<{$smarty.const.REFERENCES_IMAGES_URL}>attachment.png" alt="" border="10"/></a>
    <{/if}>
    <{$article.article_text}>
    <br><{$article.article_readmore}>
    <{if $article.article_externalurl != ''}>
        <br>
        <a href="<{$article.article_externalurl}>" target="_blank"><{$smarty.const._MD_REFERENCES_VIEW_WEBSITE}></a>
        <br>
    <{/if}>
    <{if $article.article_thumb_exists}>
        <p>
            <{section name=image loop=$article.article_pictures_urls}>
                <a href="<{$article.article_pictures_urls[image]}>" rel="lightbox-<{$article.article_id}>" title="<{$article.article_pictures_texts[image]}>"><img src="<{$article.article_thumbs_urls[image]}>"/></a>
            <{/section}>
        </p>
    <{/if}>
</div>

<{if $use_rss}>
    <div align="right">
        <a href="<{$smarty.const.REFERENCES_URL}>rss.php"><img src="<{$smarty.const.REFERENCES_IMAGES_URL}>rss.gif" alt="" border="0"/></a>
    </div>
<{/if}>
<{if $isAdmin}>
    <div align="right"><br><a href="<{$smarty.const.REFERENCES_URL}>admin/index.php?op=editarticles&id=<{$article.article_id}>"><{$smarty.const._MD_REFERENCES_ADMIN}></a></div>
<{/if}>
<{include file='db:system_notification_select.tpl'}>
