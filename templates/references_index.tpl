<!-- Created by Instant Zero (http://www.instant-zero.com) -->
<{if $use_rss}>
    <div align="right">
        <a href="<{$smarty.const.REFERENCES_URL}>rss.php"><img src="<{$smarty.const.REFERENCES_IMAGES_URL}>rss.gif" alt="" border="0"/></a>
    </div>
<{/if}>
<{if trim($welcomeMsg) != '' }>
    <div align='center'>
        <{$welcomeMsg}><br><br>
    </div>
<{/if}>
<style>
    #accordion a img {
        padding: 5px;
        border: solid 1px #cccccc;
        margin: 2px;
    }

    #accordion a img:hover {
        border: solid 1px #0066cc;
    }
</style>

<{if isset($categories) && count($categories) > 0 }>
    <div align="center"><b><{$smarty.const._MD_REFERENCES_CATEGORIES}></b> <{$categoriesSelect}><br><br></div>
    <{foreach item=category from=$categories}>
        <h2><a href="<{$smarty.const.REFERENCES_URL}>category.php?category_id=<{$category.categoryId}>"><{$category.categoryTitle}></a></h2>
        <div id="accordion">
            <{foreach item=article from=$category.articles}>
                <h3 class="toggler"><{$article.article_title}><{if trim($article.article_date) != ''}> <i>(<{$article.article_date}>)</i><{/if}></h3>
                <div class="element">
                    <{if $article.article_attachment_exists}>
                        <a href="<{$article.article_attachment_url}>" target="_blank"><img src="<{$smarty.const.REFERENCES_IMAGES_URL}>attachment.png" alt="" border="10"/></a>
                    <{/if}>
                    <{$article.article_text}>
                    <{if $article.article_externalurl != ''}>
                        <br>
                        <a href="<{$article.article_externalurl}>" target="_blank"><{$smarty.const._MD_REFERENCES_VIEW_WEBSITE}></a>
                        <br>
                    <{/if}>
                    <{if $article.article_thumb_exists}>
                        <p><{section name=image loop=$article.article_pictures_urls}>
                                <a href="<{$article.article_pictures_urls[image]}>" rel="lightbox-<{$article.article_id}>" title="<{$article.article_pictures_texts[image]}>"><img src="<{$article.article_thumbs_urls[image]}>"/></a>
                            <{/section}>
                        </p>
                    <{/if}>
                    <div align="center">
                        <a href="<{$article.article_url}>" title="<{$article.article_href_title}>"><{$smarty.const._MD_REFERENCES_SEE_REFERENCE}></a><br><br>
                    </div>
                </div>
            <{/foreach}>
        </div>
    <{/foreach}>
<{else}>
<h4><{$smarty.const._MD_REFERENCES_SORRY_NO_REF}>
    <{/if}>
    <{if $use_rss}>
        <div align="right">
            <a href="<{$smarty.const.REFERENCES_URL}>rss.php"><img src="<{$smarty.const.REFERENCES_IMAGES_URL}>rss.gif" alt="" border="0"/></a>
        </div>
    <{/if}>

    <{if isset($pagenav)}>
        <div align="center">
            <br><br>
            <{$pagenav}>
            <br><br>
        </div>
    <{/if}>
    <script type="text/javascript">
        window.addEvent('domready', function () {
            var myAccordion = new Accordion($('accordion'), 'h3.toggler', 'div.element', {
                opacity: false,
                display: false,
                alwaysHide: true,
                onActive: function (toggler, element) {
                    toggler.setStyle('color', '#41464D');
                },
                onBackground: function (toggler, element) {
                    toggler.setStyle('color', '#528CE0');
                }
            });
            myAccordion.display(<{$defaultArticle}>);
        });
    </script>

    <{if $isAdmin}>
        <div align="right"><br><a href="<{$smarty.const.REFERENCES_URL}>admin/index.php"><{$smarty.const._MD_REFERENCES_ADMIN}></a></div>
    <{/if}>
<{include file='db:system_notification_select.tpl'}>
