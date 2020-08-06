<?php
/**
 * Display banners on settings page
 * @package Captcha by BestWebSoft
 * @since 4.1.5
 */

if ( ! function_exists( 'rlt_agents_block' ) ) {
	function rlt_agents_block() { ?>
        <h2 class="screen-reader-text">Filter agents list</h2>
        <ul class="subsubsub">
            <li class="all"><a href="" class="current" aria-current="page">All <span
                            class="count">(4)</span></a> |
            </li>
            <li class="publish"><a href="">Published <span
                            class="count">(4)</span></a></li>
        </ul>
        <form id="posts-filter" method="get">
            <p class="search-box">
                <label class="screen-reader-text" for="post-search-input">Search Agents:</label>
                <input type="search" id="post-search-input" name="s" value="" disabled="disabled">
                <input type="submit" id="search-submit" class="button" value="Search Agents" disabled="disabled"></p>
            <input type="hidden" name="post_status" class="post_status_page" value="all">
            <input type="hidden" name="post_type" class="post_type_page" value="agent">
            <input type="hidden" id="_wpnonce" name="_wpnonce" value="c6b0e4cf1f"><input type="hidden"
                                                                                         name="_wp_http_referer"
                                                                                         value="">
            <div class="tablenav top">
                <div class="alignleft actions bulkactions">
                    <label for="bulk-action-selector-top" class="screen-reader-text">Select bulk action</label><select
                            name="action" id="bulk-action-selector-top" disabled="disabled">
                        <option value="-1">Bulk Actions</option>
                        <option value="edit" class="hide-if-no-js">Edit</option>
                        <option value="trash">Move to Trash</option>
                    </select>
                    <input type="submit" id="doaction" class="button action" value="Apply" disabled="disabled">
                </div>
                <div class="alignleft actions">
                    <label for="filter-by-date" class="screen-reader-text">Filter by date</label>
                    <select name="m" id="filter-by-date" disabled="disabled">
                        <option selected="selected" value="0">All dates</option>
                        <option value="202007">July 2020</option>
                    </select>
                    <input type="submit" name="filter_action" id="post-query-submit" class="button" value="Filter" disabled="disabled">
                </div>
                <div class="tablenav-pages one-page"><span class="displaying-num">4 items</span>
                    <span class="pagination-links"><span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
<span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
<span class="paging-input"><label for="current-page-selector" class="screen-reader-text">Current Page</label><input
            class="current-page" id="current-page-selector" type="text" name="paged" value="1" size="1"
            aria-describedby="table-paging" disabled="disabled"><span class="tablenav-paging-text"> of <span
                class="total-pages">1</span></span></span>
<span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>
<span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span></span></div>
                <br class="clear">
            </div>
            <h2 class="screen-reader-text">Agents list</h2>
            <table class="wp-list-table widefat fixed striped posts">
                <thead>
                <tr>
                    <td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text"
                                                                                    for="cb-select-all-1">Select
                            All</label><input id="cb-select-all-1" type="checkbox" disabled="disabled"></td>
                    <th scope="col" id="title" class="manage-column column-title column-primary sortable desc"><a
                                href=""><span>Agent</span><span
                                    class="sorting-indicator"></span></a></th>
                    <th scope="col" id="count_property" class="manage-column column-count_property">Count Property</th>
                    <th scope="col" id="date" class="manage-column column-date sortable asc"><a
                                href=""><span>Date</span><span
                                    class="sorting-indicator"></span></a></th>
                </tr>
                </thead>
                <tbody id="the-list">
                <tr id="post-111" class="iedit author-self level-0 post-111 type-agent status-publish hentry">
                    <th scope="row" class="check-column"><label class="screen-reader-text" for="cb-select-111">
                            Select DEMO John Smith </label>
                        <input id="cb-select-111" type="checkbox" name="post[]" value="111" disabled="disabled">
                        <div class="locked-indicator">
                            <span class="locked-indicator-icon" aria-hidden="true"></span>
                            <span class="screen-reader-text">
				“DEMO John Smith” is locked				</span>
                        </div>
                    </th>
                    <td class="title column-title has-row-actions column-primary page-title" data-colname="Agent">
                        <div class="locked-info"><span class="locked-avatar"></span> <span class="locked-text"></span>
                        </div>
                        <strong><a class="row-title"
                                   href=""
                                   aria-label="“DEMO John Smith” (Edit)">DEMO John Smith</a></strong>
                        <div class="hidden" id="inline_111">
                            <div class="post_title">DEMO John Smith</div>
                            <div class="post_name">john-smith</div>
                            <div class="post_author">1</div>
                            <div class="comment_status">closed</div>
                            <div class="ping_status">closed</div>
                            <div class="_status">publish</div>
                            <div class="jj">02</div>
                            <div class="mm">07</div>
                            <div class="aa">2020</div>
                            <div class="hh">12</div>
                            <div class="mn">05</div>
                            <div class="ss">24</div>
                            <div class="post_password"></div>
                            <div class="page_template">default</div>
                            <div class="sticky"></div>
                        </div>
                        <div class="row-actions"><span class="edit"><a
                                        href=""
                                        aria-label="Edit “DEMO John Smith”">Edit</a> | </span><span
                                    class="inline hide-if-no-js"><button type="button" class="button-link editinline"
                                                                         aria-label="Quick edit “DEMO John Smith” inline"
                                                                         aria-expanded="false">Quick&nbsp;Edit</button> | </span><span
                                    class="trash"><a
                                        href=""
                                        class="submitdelete" aria-label="Move “DEMO John Smith” to the Trash">Trash</a> | </span><span
                                    class="view"><a href="" rel="bookmark"
                                                    aria-label="View “DEMO John Smith”">View</a></span></div>
                        <button type="button" class="toggle-row"><span
                                    class="screen-reader-text">Show more details</span></button>
                    </td>
                    <td class="count_property column-count_property" data-colname="Count Property">7 property(ies)</td>
                    <td class="date column-date" data-colname="Date">Published<br><span title="2020/07/02 12:05:24 pm">2020/07/02</span>
                    </td>
                </tr>
                <tr id="post-112" class="iedit author-self level-0 post-112 type-agent status-publish hentry">
                    <th scope="row" class="check-column"><label class="screen-reader-text" for="cb-select-112">
                            Select DEMO New Rivendell </label>
                        <input id="cb-select-112" type="checkbox" name="post[]" value="112" disabled="disabled">
                        <div class="locked-indicator">
                            <span class="locked-indicator-icon" aria-hidden="true"></span>
                            <span class="screen-reader-text">
				“DEMO New Rivendell” is locked				</span>
                        </div>
                    </th>
                    <td class="title column-title has-row-actions column-primary page-title" data-colname="Agent">
                        <div class="locked-info"><span class="locked-avatar"></span> <span class="locked-text"></span>
                        </div>
                        <strong><a class="row-title"
                                   href=""
                                   aria-label="“DEMO New Rivendell” (Edit)">DEMO New Rivendell</a></strong>
                        <div class="hidden" id="inline_112">
                            <div class="post_title">DEMO New Rivendell</div>
                            <div class="post_name">new-rivendell</div>
                            <div class="post_author">1</div>
                            <div class="comment_status">closed</div>
                            <div class="ping_status">closed</div>
                            <div class="_status">publish</div>
                            <div class="jj">02</div>
                            <div class="mm">07</div>
                            <div class="aa">2020</div>
                            <div class="hh">12</div>
                            <div class="mn">05</div>
                            <div class="ss">24</div>
                            <div class="post_password"></div>
                            <div class="page_template">default</div>
                            <div class="sticky"></div>
                        </div>
                        <div class="row-actions"><span class="edit"><a
                                        href=""
                                        aria-label="Edit “DEMO New Rivendell”">Edit</a> | </span><span
                                    class="inline hide-if-no-js"><button type="button" class="button-link editinline"
                                                                         aria-label="Quick edit “DEMO New Rivendell” inline"
                                                                         aria-expanded="false">Quick&nbsp;Edit</button> | </span><span
                                    class="trash"><a
                                        href=""
                                        class="submitdelete"
                                        aria-label="Move “DEMO New Rivendell” to the Trash">Trash</a> | </span><span
                                    class="view"><a href="" rel="bookmark"
                                                    aria-label="View “DEMO New Rivendell”">View</a></span></div>
                        <button type="button" class="toggle-row"><span
                                    class="screen-reader-text">Show more details</span></button>
                    </td>
                    <td class="count_property column-count_property" data-colname="Count Property">7 property(ies)</td>
                    <td class="date column-date" data-colname="Date">Published<br><span title="2020/07/02 12:05:24 pm">2020/07/02</span>
                    </td>
                </tr>
                <tr id="post-113" class="iedit author-self level-0 post-113 type-agent status-publish hentry">
                    <th scope="row" class="check-column"><label class="screen-reader-text" for="cb-select-113">
                            Select DEMO Main Realty </label>
                        <input id="cb-select-113" type="checkbox" name="post[]" value="113" disabled="disabled">
                        <div class="locked-indicator">
                            <span class="locked-indicator-icon" aria-hidden="true"></span>
                            <span class="screen-reader-text">
				“DEMO Main Realty” is locked				</span>
                        </div>
                    </th>
                    <td class="title column-title has-row-actions column-primary page-title" data-colname="Agent">
                        <div class="locked-info"><span class="locked-avatar"></span> <span class="locked-text"></span>
                        </div>
                        <strong><a class="row-title"
                                   href=""
                                   aria-label="“DEMO Main Realty” (Edit)">DEMO Main Realty</a></strong>

                        <div class="hidden" id="inline_113">
                            <div class="post_title">DEMO Main Realty</div>
                            <div class="post_name">main-realty</div>
                            <div class="post_author">1</div>
                            <div class="comment_status">closed</div>
                            <div class="ping_status">closed</div>
                            <div class="_status">publish</div>
                            <div class="jj">02</div>
                            <div class="mm">07</div>
                            <div class="aa">2020</div>
                            <div class="hh">12</div>
                            <div class="mn">05</div>
                            <div class="ss">24</div>
                            <div class="post_password"></div>
                            <div class="page_template">default</div>
                            <div class="sticky"></div>
                        </div>
                        <div class="row-actions"><span class="edit"><a
                                        href=""
                                        aria-label="Edit “DEMO Main Realty”">Edit</a> | </span><span
                                    class="inline hide-if-no-js"><button type="button" class="button-link editinline"
                                                                         aria-label="Quick edit “DEMO Main Realty” inline"
                                                                         aria-expanded="false">Quick&nbsp;Edit</button> | </span><span
                                    class="trash"><a
                                        href=""
                                        class="submitdelete" aria-label="Move “DEMO Main Realty” to the Trash">Trash</a> | </span><span
                                    class="view"><a href="" rel="bookmark"
                                                    aria-label="View “DEMO Main Realty”">View</a></span></div>
                        <button type="button" class="toggle-row"><span
                                    class="screen-reader-text">Show more details</span></button>
                    </td>
                    <td class="count_property column-count_property" data-colname="Count Property">7 property(ies)</td>
                    <td class="date column-date" data-colname="Date">Published<br><span title="2020/07/02 12:05:24 pm">2020/07/02</span>
                    </td>
                </tr>
                <tr id="post-114" class="iedit author-self level-0 post-114 type-agent status-publish hentry">
                    <th scope="row" class="check-column"><label class="screen-reader-text" for="cb-select-114">
                            Select DEMO New Era </label>
                        <input id="cb-select-114" type="checkbox" name="post[]" value="114" disabled="disabled">
                        <div class="locked-indicator">
                            <span class="locked-indicator-icon" aria-hidden="true"></span>
                            <span class="screen-reader-text">
				“DEMO New Era” is locked				</span>
                        </div>
                    </th>
                    <td class="title column-title has-row-actions column-primary page-title" data-colname="Agent">
                        <div class="locked-info"><span class="locked-avatar"></span> <span class="locked-text"></span>
                        </div>
                        <strong><a class="row-title"
                                   href=""
                                   aria-label="“DEMO New Era” (Edit)">DEMO New Era</a></strong>
                        <div class="hidden" id="inline_114">
                            <div class="post_title">DEMO New Era</div>
                            <div class="post_name">new-era</div>
                            <div class="post_author">1</div>
                            <div class="comment_status">closed</div>
                            <div class="ping_status">closed</div>
                            <div class="_status">publish</div>
                            <div class="jj">02</div>
                            <div class="mm">07</div>
                            <div class="aa">2020</div>
                            <div class="hh">12</div>
                            <div class="mn">05</div>
                            <div class="ss">24</div>
                            <div class="post_password"></div>
                            <div class="page_template">default</div>
                            <div class="sticky"></div>
                        </div>
                        <div class="row-actions"><span class="edit"><a
                                        href=""
                                        aria-label="Edit “DEMO New Era”">Edit</a> | </span><span
                                    class="inline hide-if-no-js"><button type="button" class="button-link editinline"
                                                                         aria-label="Quick edit “DEMO New Era” inline"
                                                                         aria-expanded="false">Quick&nbsp;Edit</button> | </span><span
                                    class="trash"><a
                                        href=""
                                        class="submitdelete" aria-label="Move “DEMO New Era” to the Trash">Trash</a> | </span><span
                                    class="view"><a href="" rel="bookmark"
                                                    aria-label="View “DEMO New Era”">View</a></span></div>
                        <button type="button" class="toggle-row"><span
                                    class="screen-reader-text">Show more details</span></button>
                    </td>
                    <td class="count_property column-count_property" data-colname="Count Property">6 property(ies)</td>
                    <td class="date column-date" data-colname="Date">Published<br><span title="2020/07/02 12:05:24 pm">2020/07/02</span>
                    </td>
                </tr>
                </tbody>
                <tfoot>
                <tr>
                    <td class="manage-column column-cb check-column"><label class="screen-reader-text"
                                                                            for="cb-select-all-2">Select
                            All</label><input id="cb-select-all-2" type="checkbox" disabled="disabled"></td>
                    <th scope="col" class="manage-column column-title column-primary sortable desc"><a
                                href=""><span>Agent</span><span
                                    class="sorting-indicator"></span></a></th>
                    <th scope="col" class="manage-column column-count_property">Count Property</th>
                    <th scope="col" class="manage-column column-date sortable asc"><a
                                href=""><span>Date</span><span
                                    class="sorting-indicator"></span></a></th>
                </tr>
                </tfoot>
            </table>
            <div class="tablenav bottom">
                <div class="alignleft actions bulkactions">
                    <label for="bulk-action-selector-bottom" class="screen-reader-text">Select bulk
                        action</label><select name="action2" id="bulk-action-selector-bottom" disabled="disabled">
                        <option value="-1">Bulk Actions</option>
                        <option value="edit" class="hide-if-no-js">Edit</option>
                        <option value="trash">Move to Trash</option>
                    </select>
                    <input type="submit" id="doaction2" class="button action" value="Apply" disabled="disabled">
                </div>
                <div class="alignleft actions">
                </div>
                <div class="tablenav-pages one-page"><span class="displaying-num">4 items</span>
                    <span class="pagination-links"><span class="tablenav-pages-navspan button disabled"
                                                         aria-hidden="true">«</span>
<span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
<span class="screen-reader-text">Current Page</span><span id="table-paging" class="paging-input" disabled="disabled"><span
                                    class="tablenav-paging-text">1 of <span class="total-pages">1</span></span></span>
<span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>
<span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span></span></div>
                <br class="clear">
            </div>
        </form>
        <form method="get">
            <table style="display: none">
                <tbody id="inlineedit">
                <tr id="inline-edit"
                    class="inline-edit-row inline-edit-row-page quick-edit-row quick-edit-row-page inline-edit-agent"
                    style="display: none">
                    <td colspan="4" class="colspanchange">

                        <fieldset class="inline-edit-col-left">
                            <legend class="inline-edit-legend">Quick Edit</legend>
                            <div class="inline-edit-col">
                                <label>
                                    <span class="title">Title</span>
                                    <span class="input-text-wrap"><input type="text" name="post_title" class="ptitle"
                                                                         value="" disabled="disabled"></span>
                                </label>
                                <label>
                                    <span class="title">Slug</span>
                                    <span class="input-text-wrap"><input type="text" name="post_name" value="" disabled="disabled"></span>
                                </label>
                                <fieldset class="inline-edit-date">
                                    <legend><span class="title">Date</span></legend>
                                    <div class="timestamp-wrap"><label><span
                                                    class="screen-reader-text">Month</span><select name="mm">
                                                <option value="01" data-text="Jan">01-Jan</option>
                                                <option value="02" data-text="Feb">02-Feb</option>
                                                <option value="03" data-text="Mar">03-Mar</option>
                                                <option value="04" data-text="Apr">04-Apr</option>
                                                <option value="05" data-text="May">05-May</option>
                                                <option value="06" data-text="Jun">06-Jun</option>
                                                <option value="07" data-text="Jul" selected="selected">07-Jul</option>
                                                <option value="08" data-text="Aug">08-Aug</option>
                                                <option value="09" data-text="Sep">09-Sep</option>
                                                <option value="10" data-text="Oct">10-Oct</option>
                                                <option value="11" data-text="Nov">11-Nov</option>
                                                <option value="12" data-text="Dec">12-Dec</option>
                                            </select></label> <label><span class="screen-reader-text">Day</span><input
                                                    type="text" name="jj" value="02" size="2" maxlength="2"
                                                    autocomplete="off" disabled="disabled"></label>, <label><span
                                                    class="screen-reader-text">Year</span><input type="text" name="aa"
                                                                                                 value="2020" size="4"
                                                                                                 maxlength="4"
                                                                                                 autocomplete="off" disabled="disabled"></label>
                                        at <label><span class="screen-reader-text">Hour</span><input type="text"
                                                                                                     name="hh"
                                                                                                     value="12" size="2"
                                                                                                     maxlength="2"
                                                                                                     autocomplete="off" disabled="disabled"></label>:<label><span
                                                    class="screen-reader-text">Minute</span><input type="text" name="mn"
                                                                                                   value="05" size="2"
                                                                                                   maxlength="2"
                                                                                                   autocomplete="off" disabled="disabled"></label>
                                    </div>
                                    <input type="hidden" id="ss" name="ss" value="24" disabled="disabled"></fieldset>
                                <br class="clear">
                                <div class="inline-edit-group wp-clearfix">
                                    <label class="alignleft">
                                        <span class="title">Password</span>
                                        <span class="input-text-wrap"><input type="text" name="post_password"
                                                                             class="inline-edit-password-input"
                                                                             value="" disabled="disabled"></span>
                                    </label>

                                    <span class="alignleft inline-edit-or">
							–OR–						</span>
                                    <label class="alignleft inline-edit-private">
                                        <input type="checkbox" name="keep_private" value="private" disabled="disabled">
                                        <span class="checkbox-title">Private</span>
                                    </label>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset class="inline-edit-col-right">
                            <div class="inline-edit-col">
                                <div class="inline-edit-group wp-clearfix">
                                    <label class="inline-edit-status alignleft">
                                        <span class="title">Status</span>
                                        <select name="_status">

                                            <option value="publish">Published</option>
                                            <option value="future">Scheduled</option>

                                            <option value="pending">Pending Review</option>
                                            <option value="draft">Draft</option>
                                        </select>
                                    </label>
                                </div>
                            </div>
                        </fieldset>
                        <div class="submit inline-edit-save">
                            <button type="button" class="button cancel alignleft">Cancel</button>
                            <input type="hidden" id="_inline_edit" name="_inline_edit" value="5bfe15c014">
                            <button type="button" class="button button-primary save alignright">Update</button>
                            <span class="spinner"></span>
                            <input type="hidden" name="post_view" value="list">
                            <input type="hidden" name="screen" value="edit-agent">
                            <input type="hidden" name="post_author" value="">
                            <br class="clear">
                            <div class="notice notice-error notice-alt inline hidden">
                                <p class="error"></p>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr id="bulk-edit"
                    class="inline-edit-row inline-edit-row-page bulk-edit-row bulk-edit-row-page bulk-edit-agent"
                    style="display: none">
                    <td colspan="4" class="colspanchange">
                        <fieldset class="inline-edit-col-left">
                            <legend class="inline-edit-legend">Bulk Edit</legend>
                            <div class="inline-edit-col">
                                <div id="bulk-title-div">
                                    <div id="bulk-titles"></div>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset class="inline-edit-col-right">
                            <div class="inline-edit-col">
                                <div class="inline-edit-group wp-clearfix">
                                    <label class="inline-edit-status alignleft">
                                        <span class="title">Status</span>
                                        <select name="_status">
                                            <option value="-1">— No Change —</option>

                                            <option value="publish">Published</option>

                                            <option value="private">Private</option>

                                            <option value="pending">Pending Review</option>
                                            <option value="draft">Draft</option>
                                        </select>
                                    </label>
                                </div>
                            </div>
                        </fieldset>
                        <div class="submit inline-edit-save">
                            <button type="button" class="button cancel alignleft">Cancel</button>

                            <input type="submit" name="bulk_edit" id="bulk_edit"
                                   class="button button-primary alignright" value="Update" disabled="disabled">
                            <input type="hidden" name="post_view" value="list">
                            <input type="hidden" name="screen" value="edit-agent">
                            <br class="clear">

                            <div class="notice notice-error notice-alt inline hidden">
                                <p class="error"></p>
                            </div>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
        <div id="ajax-response"></div>
        <br class="clear">
	<?php }
}

if ( ! function_exists( 'rlt_agents_add_new_block' ) ) {
	function rlt_agents_add_new_block() { ?>
            <hr class="wp-header-end"><div id="lost-connection-notice" class="error hidden">
                <p><span class="spinner"></span> <strong>Connection lost.</strong> Saving has been disabled until you’re reconnected.	<span class="hide-if-no-sessionstorage">We’re backing up this post in your browser, just in case.</span>
                </p>
            </div><div id="local-storage-notice" class="hidden notice is-dismissible">
                <p class="local-restore">
                    The backup of this post in your browser is different from the version below.		<button type="button" class="button restore-backup">Restore the backup</button>
                </p>
                <p class="help">
                    This will replace the current editor content with the last backup version. You can use undo and redo in the editor to get the old content back or to return to the restored version.	</p>
                <button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>


            <form name="post" action="" method="post" id="post">
                <style>
                    .wp-editor-tools{
                        position: initial !important;
                    }
                </style>
                <div id="poststuff">
                    <div id="post-body" class="metabox-holder columns-2">
                        <div id="post-body-content" style="position: relative;">

                            <div id="titlediv">
                                <div id="titlewrap">
                                    <label class="" id="title-prompt-text" for="title">Add title</label>
                                    <input type="text" name="post_title" size="30" value="" id="title" spellcheck="true" autocomplete="off" disabled="disabled">
                                </div>
                                <div class="inside">
                                    <div id="edit-slug-box" class="hide-if-no-js">
                                    </div>
                                </div>
                                <input type="hidden" id="samplepermalinknonce" name="samplepermalinknonce" value="4049bdfe95"></div><!-- /titlediv -->
	                        <?php if ( function_exists( 'wp_editor' ) ) {
		                        $settings = array(
			                        'wpautop'		=> 1,
			                        'media_buttons'	=> 1,
			                        'textarea_name'	=> 'rlt_top',
			                        'textarea_rows'	=> 5,
			                        'tabindex'		=> null,
			                        'editor_css'	=> '<style>.mce-content-body { width: 100%; max-width: 100%; background: red;}</style>',
			                        'editor_class'	=> 'rlt_top',
			                        'teeny'			=> 0,
			                        'dfw'			=> 0,
			                        'tinymce'		=> 1,
			                        'quicktags'		=> 1
		                        );
		                        wp_editor( '', 'rlt_top', $settings );
	                        } else { ?>
                                <textarea disabled="disabled" class="rlt_top_area" rows="5" autocomplete="off" cols="40" name="rlt_top" id="rlt_top"></textarea>
	                        <?php }?>
                        </div><!-- /post-body-content -->

                        <div id="postbox-container-1" class="postbox-container">
                            <div id="side-sortables" class="meta-box-sortables ui-sortable" style=""><div id="submitdiv" class="postbox ">
                                    <button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Toggle panel: Publish</span><span class="toggle-indicator" aria-hidden="true"></span></button><h2 class="hndle ui-sortable-handle"><span>Publish</span></h2>
                                    <div class="inside">
                                        <div class="submitbox" id="submitpost">

                                            <div id="minor-publishing">

                                                <div style="display:none;">
                                                    <p class="submit"><input type="submit" name="save" id="save" class="button" value="Save"></p></div>

                                                <div id="minor-publishing-actions">
                                                    <div id="save-action">
                                                        <input type="submit" name="save" id="save-post" value="Save Draft" class="button" disabled="disabled">
                                                        <span class="spinner"></span>
                                                    </div>
                                                    <div id="preview-action">
                                                        <a class="preview button" href="" target="wp-preview-150" id="post-preview">Preview<span class="screen-reader-text"> (opens in a new tab)</span></a>
                                                        <input type="hidden" name="wp-preview" id="wp-preview" value="">
                                                    </div>
                                                    <div class="clear"></div>
                                                </div><!-- #minor-publishing-actions -->

                                                <div id="misc-publishing-actions">

                                                    <div class="misc-pub-section misc-pub-post-status">
                                                        Status: <span id="post-status-display">
			Draft</span>
                                                        <a href="" class="edit-post-status hide-if-no-js" role="button"><span aria-hidden="true">Edit</span> <span class="screen-reader-text">Edit status</span></a>

                                                        <div id="post-status-select" class="hide-if-js">
                                                            <input type="hidden" name="hidden_post_status" id="hidden_post_status" value="draft">
                                                            <label for="post_status" class="screen-reader-text">Set status</label>
                                                            <select name="post_status" id="post_status">
                                                                <option value="pending">Pending Review</option>
                                                                <option selected="selected" value="draft">Draft</option>
                                                            </select>
                                                            <a href="" class="save-post-status hide-if-no-js button">OK</a>
                                                            <a href="" class="cancel-post-status hide-if-no-js button-cancel">Cancel</a>
                                                        </div>

                                                    </div><!-- .misc-pub-section -->

                                                    <div class="misc-pub-section misc-pub-visibility" id="visibility">
                                                        Visibility: <span id="post-visibility-display">
							Public</span>
                                                        <a href="" class="edit-visibility hide-if-no-js" role="button"><span aria-hidden="true">Edit</span> <span class="screen-reader-text">Edit visibility</span></a>

                                                        <div id="post-visibility-select" class="hide-if-js">
                                                            <input type="hidden" name="hidden_post_password" id="hidden-post-password" value="">
                                                            <input type="hidden" name="hidden_post_visibility" id="hidden-post-visibility" value="public">
                                                            <input type="radio" name="visibility" id="visibility-radio-public" value="public" checked="checked"> <label for="visibility-radio-public" class="selectit">Public</label><br>
                                                            <input type="radio" name="visibility" id="visibility-radio-password" value="password"> <label for="visibility-radio-password" class="selectit">Password protected</label><br>
                                                            <span id="password-span"><label for="post_password">Password:</label> <input type="text" name="post_password" id="post_password" value="" maxlength="255"><br></span>
                                                            <input type="radio" name="visibility" id="visibility-radio-private" value="private"> <label for="visibility-radio-private" class="selectit">Private</label><br>

                                                            <p>
                                                                <a href="" class="save-post-visibility hide-if-no-js button">OK</a>
                                                                <a href="" class="cancel-post-visibility hide-if-no-js button-cancel">Cancel</a>
                                                            </p>
                                                        </div>

                                                    </div><!-- .misc-pub-section -->

                                                    <div class="misc-pub-section curtime misc-pub-curtime">
	<span id="timestamp">
		Publish <b>immediately</b>	</span>
                                                        <a href="" class="edit-timestamp hide-if-no-js" role="button">
                                                            <span aria-hidden="true">Edit</span>
                                                            <span class="screen-reader-text">Edit date and time</span>
                                                        </a>
                                                        <fieldset id="timestampdiv" class="hide-if-js">
                                                            <legend class="screen-reader-text">Date and time</legend>
                                                            <div class="timestamp-wrap"><label><span class="screen-reader-text">Month</span><select id="mm" name="mm">
                                                                        <option value="01" data-text="Jan">01-Jan</option>
                                                                        <option value="02" data-text="Feb">02-Feb</option>
                                                                        <option value="03" data-text="Mar">03-Mar</option>
                                                                        <option value="04" data-text="Apr">04-Apr</option>
                                                                        <option value="05" data-text="May">05-May</option>
                                                                        <option value="06" data-text="Jun">06-Jun</option>
                                                                        <option value="07" data-text="Jul" selected="selected">07-Jul</option>
                                                                        <option value="08" data-text="Aug">08-Aug</option>
                                                                        <option value="09" data-text="Sep">09-Sep</option>
                                                                        <option value="10" data-text="Oct">10-Oct</option>
                                                                        <option value="11" data-text="Nov">11-Nov</option>
                                                                        <option value="12" data-text="Dec">12-Dec</option>
                                                                    </select></label> <label><span class="screen-reader-text">Day</span><input type="text" id="jj" name="jj" value="14" size="2" maxlength="2" autocomplete="off"></label>, <label><span class="screen-reader-text">Year</span><input type="text" id="aa" name="aa" value="2020" size="4" maxlength="4" autocomplete="off"></label> at <label><span class="screen-reader-text">Hour</span><input type="text" id="hh" name="hh" value="13" size="2" maxlength="2" autocomplete="off" disabled="disabled"></label>:<label><span class="screen-reader-text">Minute</span><input type="text" id="mn" name="mn" value="39" size="2" maxlength="2" autocomplete="off" disabled="disabled"></label></div><input type="hidden" id="ss" name="ss" value="10">

                                                            <p>
                                                                <a href="" class="save-timestamp hide-if-no-js button">OK</a>
                                                                <a href="" class="cancel-timestamp hide-if-no-js button-cancel">Cancel</a>
                                                            </p>
                                                        </fieldset>
                                                    </div>

                                                </div>
                                                <div class="clear"></div>
                                            </div>

                                            <div id="major-publishing-actions">
                                                <div id="delete-action">
                                                    <a class="submitdelete deletion" href="">Move to Trash</a>
                                                </div>

                                                <div id="publishing-action">
                                                    <span class="spinner"></span>
                                                    <input type="submit" name="publish" id="publish" class="button button-primary button-large" value="Publish" disabled="disabled">		</div>
                                                <div class="clear"></div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div></div>
                        <div id="postbox-container-2" class="postbox-container">
                            <div id="normal-sortables" class="meta-box-sortables ui-sortable"><div id="agent-custom-metabox" class="postbox ">
                                    <button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Toggle panel: Agent Info</span><span class="toggle-indicator" aria-hidden="true"></span></button><h2 class="hndle ui-sortable-handle"><span>Agent Info</span></h2>
                                    <div class="inside">
                                        <table class="form-table rlt-agent-info">
                                            <tbody><tr>
                                                <th><label for="rlt_company">Company</label><br></th>
                                                <td>
                                                    <input type="text" id="rlt_company" size="50" name="rlt_company" value="" disabled="disabled">
                                                </td>
                                            </tr>
                                            <tr>
                                                <th><label for="rlt_location">Location</label></th>
                                                <td>
                                                    <input type="text" id="rlt_location" size="50" name="rlt_location" value="" disabled="disabled">
                                                </td>
                                            </tr>
                                            <tr>
                                                <th><label for="rlt_tel">Telephone</label></th>
                                                <td>
                                                    <input type="text" id="rlt_tel" size="50" name="rlt_tel" value="" disabled="disabled">
                                                </td>
                                            </tr>
                                            <tr>
                                                <th><label for="rlt_email">Email</label></th>
                                                <td>
                                                    <input type="email" id="rlt_email" size="50" name="rlt_email" value="" disabled="disabled">
                                                </td>
                                            </tr>
                                            <tr>
                                                <th><label for="rlt_additional_info">Additional info</label></th>
                                                <td>
                                                    <div id="wp-rlt_additional_info-wrap" class="wp-core-ui wp-editor-wrap tmce-active"><div id="wp-rlt_additional_info-editor-tools" class="wp-editor-tools hide-if-no-js"><div class="wp-editor-tabs"><button type="button" id="rlt_additional_info-tmce" class="wp-switch-editor switch-tmce" data-wp-editor-id="rlt_additional_info">Visual</button>
                                                                <button type="button" id="rlt_additional_info-html" class="wp-switch-editor switch-html" data-wp-editor-id="rlt_additional_info">Text</button>
                                                            </div>
                                                        </div>
                                                        <div id="wp-rlt_additional_info-editor-container" class="wp-editor-container"><div id="qt_rlt_additional_info_toolbar" class="quicktags-toolbar"><input type="button" id="qt_rlt_additional_info_strong" class="ed_button button button-small" aria-label="Bold" value="b" disabled="disabled"><input type="button" id="qt_rlt_additional_info_em" class="ed_button button button-small" aria-label="Italic" value="i" disabled="disabled"><input type="button" id="qt_rlt_additional_info_link" class="ed_button button button-small" aria-label="Insert link" value="link" disabled="disabled"><input type="button" id="qt_rlt_additional_info_block" class="ed_button button button-small" aria-label="Blockquote" value="b-quote" disabled="disabled"><input type="button" id="qt_rlt_additional_info_del" class="ed_button button button-small" aria-label="Deleted text (strikethrough)" value="del" disabled="disabled"><input type="button" id="qt_rlt_additional_info_ins" class="ed_button button button-small" aria-label="Inserted text" value="ins" disabled="disabled"><input type="button" id="qt_rlt_additional_info_img" class="ed_button button button-small" aria-label="Insert image" value="img" disabled="disabled"><input type="button" id="qt_rlt_additional_info_ul" class="ed_button button button-small" aria-label="Bulleted list" value="ul" disabled="disabled"><input type="button" id="qt_rlt_additional_info_ol" class="ed_button button button-small" aria-label="Numbered list" value="ol" disabled="disabled"><input type="button" id="qt_rlt_additional_info_li" class="ed_button button button-small" aria-label="List item" value="li" disabled="disabled"><input type="button" id="qt_rlt_additional_info_code" class="ed_button button button-small" aria-label="Code" value="code" disabled="disabled"><input type="button" id="qt_rlt_additional_info_more" class="ed_button button button-small" aria-label="Insert Read More tag" value="more" disabled="disabled"><input type="button" id="qt_rlt_additional_info_close" class="ed_button button button-small" title="Close all open tags" value="close tags" disabled="disabled"></div><div id="mceu_84" class="mce-tinymce mce-container mce-panel" hidefocus="1" tabindex="-1" role="application" style="visibility: hidden; border-width: 1px; width: 100%;"><div id="mceu_84-body" class="mce-container-body mce-stack-layout"><div id="mceu_85" class="mce-top-part mce-container mce-stack-layout-item mce-first"><div id="mceu_85-body" class="mce-container-body"><div id="mceu_86" class="mce-toolbar-grp mce-container mce-panel mce-first mce-last" hidefocus="1" tabindex="-1" role="group"><div id="mceu_86-body" class="mce-container-body mce-stack-layout"><div id="mceu_87" class="mce-container mce-toolbar mce-stack-layout-item mce-first" role="toolbar"><div id="mceu_87-body" class="mce-container-body mce-flow-layout"><div id="mceu_88" class="mce-container mce-flow-layout-item mce-first mce-last mce-btn-group" role="group"><div id="mceu_88-body"><div id="mceu_60" class="mce-widget mce-btn mce-menubtn mce-fixed-width mce-listbox mce-first mce-btn-has-text" tabindex="-1" aria-labelledby="mceu_60" role="button" aria-haspopup="true"><button id="mceu_60-open" role="presentation" type="button" tabindex="-1"><span class="mce-txt">Paragraph</span> <i class="mce-caret"></i></button></div><div id="mceu_61" class="mce-widget mce-btn" tabindex="-1" aria-pressed="false" role="button" aria-label="Bold (Ctrl+B)"><button id="mceu_61-button" role="presentation" type="button" tabindex="-1"><i class="mce-ico mce-i-bold"></i></button></div><div id="mceu_62" class="mce-widget mce-btn" tabindex="-1" aria-pressed="false" role="button" aria-label="Italic (Ctrl+I)"><button id="mceu_62-button" role="presentation" type="button" tabindex="-1"><i class="mce-ico mce-i-italic"></i></button></div><div id="mceu_63" class="mce-widget mce-btn" tabindex="-1" aria-pressed="false" role="button" aria-label="Bulleted list (Shift+Alt+U)"><button id="mceu_63-button" role="presentation" type="button" tabindex="-1"><i class="mce-ico mce-i-bullist"></i></button></div><div id="mceu_64" class="mce-widget mce-btn" tabindex="-1" aria-pressed="false" role="button" aria-label="Numbered list (Shift+Alt+O)"><button id="mceu_64-button" role="presentation" type="button" tabindex="-1"><i class="mce-ico mce-i-numlist"></i></button></div><div id="mceu_65" class="mce-widget mce-btn" tabindex="-1" aria-pressed="false" role="button" aria-label="Blockquote (Shift+Alt+Q)"><button id="mceu_65-button" role="presentation" type="button" tabindex="-1"><i class="mce-ico mce-i-blockquote"></i></button></div><div id="mceu_66" class="mce-widget mce-btn" tabindex="-1" aria-pressed="false" role="button" aria-label="Align left (Shift+Alt+L)"><button id="mceu_66-button" role="presentation" type="button" tabindex="-1"><i class="mce-ico mce-i-alignleft"></i></button></div><div id="mceu_67" class="mce-widget mce-btn" tabindex="-1" aria-pressed="false" role="button" aria-label="Align center (Shift+Alt+C)"><button id="mceu_67-button" role="presentation" type="button" tabindex="-1"><i class="mce-ico mce-i-aligncenter"></i></button></div><div id="mceu_68" class="mce-widget mce-btn" tabindex="-1" aria-pressed="false" role="button" aria-label="Align right (Shift+Alt+R)"><button id="mceu_68-button" role="presentation" type="button" tabindex="-1"><i class="mce-ico mce-i-alignright"></i></button></div><div id="mceu_69" class="mce-widget mce-btn" tabindex="-1" aria-pressed="false" role="button" aria-label="Insert/edit link (Ctrl+K)"><button id="mceu_69-button" role="presentation" type="button" tabindex="-1"><i class="mce-ico mce-i-link"></i></button></div><div id="mceu_70" class="mce-widget mce-btn" tabindex="-1" role="button" aria-label="Insert Read More tag (Shift+Alt+T)"><button id="mceu_70-button" role="presentation" type="button" tabindex="-1"><i class="mce-ico mce-i-wp_more"></i></button></div><div id="mceu_71" class="mce-widget mce-btn" tabindex="-1" aria-pressed="false" role="button" aria-label="Fullscreen"><button id="mceu_71-button" role="presentation" type="button" tabindex="-1"><i class="mce-ico mce-i-fullscreen"></i></button></div><div id="mceu_72" class="mce-widget mce-btn mce-last" tabindex="-1" role="button" aria-label="Toolbar Toggle (Shift+Alt+Z)"><button id="mceu_72-button" role="presentation" type="button" tabindex="-1"><i class="mce-ico mce-i-wp_adv"></i></button></div></div></div></div></div><div id="mceu_89" class="mce-container mce-toolbar mce-stack-layout-item mce-last" role="toolbar" style="display: none;"><div id="mceu_89-body" class="mce-container-body mce-flow-layout"><div id="mceu_90" class="mce-container mce-flow-layout-item mce-first mce-last mce-btn-group" role="group"><div id="mceu_90-body"><div id="mceu_73" class="mce-widget mce-btn mce-first" tabindex="-1" aria-pressed="false" role="button" aria-label="Strikethrough (Shift+Alt+D)"><button id="mceu_73-button" role="presentation" type="button" tabindex="-1"><i class="mce-ico mce-i-strikethrough"></i></button></div><div id="mceu_74" class="mce-widget mce-btn" tabindex="-1" role="button" aria-label="Horizontal line"><button id="mceu_74-button" role="presentation" type="button" tabindex="-1"><i class="mce-ico mce-i-hr"></i></button></div><div id="mceu_75" class="mce-widget mce-btn mce-splitbtn mce-colorbutton" role="button" tabindex="-1" aria-haspopup="true" aria-label="Text color"><button role="presentation" hidefocus="1" type="button" tabindex="-1"><i class="mce-ico mce-i-forecolor"></i><span id="mceu_75-preview" class="mce-preview"></span></button><button type="button" class="mce-open" hidefocus="1" tabindex="-1"> <i class="mce-caret"></i></button></div><div id="mceu_76" class="mce-widget mce-btn" tabindex="-1" aria-pressed="false" role="button" aria-label="Paste as text"><button id="mceu_76-button" role="presentation" type="button" tabindex="-1"><i class="mce-ico mce-i-pastetext"></i></button></div><div id="mceu_77" class="mce-widget mce-btn" tabindex="-1" role="button" aria-label="Clear formatting"><button id="mceu_77-button" role="presentation" type="button" tabindex="-1"><i class="mce-ico mce-i-removeformat"></i></button></div><div id="mceu_78" class="mce-widget mce-btn" tabindex="-1" role="button" aria-label="Special character"><button id="mceu_78-button" role="presentation" type="button" tabindex="-1"><i class="mce-ico mce-i-charmap"></i></button></div><div id="mceu_79" class="mce-widget mce-btn" tabindex="-1" role="button" aria-label="Decrease indent"><button id="mceu_79-button" role="presentation" type="button" tabindex="-1"><i class="mce-ico mce-i-outdent"></i></button></div><div id="mceu_80" class="mce-widget mce-btn" tabindex="-1" role="button" aria-label="Increase indent"><button id="mceu_80-button" role="presentation" type="button" tabindex="-1"><i class="mce-ico mce-i-indent"></i></button></div><div id="mceu_81" class="mce-widget mce-btn mce-disabled" tabindex="-1" role="button" aria-label="Undo (Ctrl+Z)" aria-disabled="true"><button id="mceu_81-button" role="presentation" type="button" tabindex="-1"><i class="mce-ico mce-i-undo"></i></button></div><div id="mceu_82" class="mce-widget mce-btn mce-disabled" tabindex="-1" role="button" aria-label="Redo (Ctrl+Y)" aria-disabled="true"><button id="mceu_82-button" role="presentation" type="button" tabindex="-1"><i class="mce-ico mce-i-redo"></i></button></div><div id="mceu_83" class="mce-widget mce-btn mce-last" tabindex="-1" role="button" aria-label="Keyboard Shortcuts (Shift+Alt+H)"><button id="mceu_83-button" role="presentation" type="button" tabindex="-1"><i class="mce-ico mce-i-wp_help"></i></button></div></div></div></div></div></div></div></div></div><div id="mceu_91" class="mce-edit-area mce-container mce-panel mce-stack-layout-item" hidefocus="1" tabindex="-1" role="group" style="border-width: 1px 0px 0px;"><iframe id="rlt_additional_info_ifr" frameborder="0" allowtransparency="true" title="Rich Text Area. Press Alt-Shift-H for help." style="width: 100%; height: 434px; display: block;"></iframe></div><div id="mceu_92" class="mce-statusbar mce-container mce-panel mce-stack-layout-item mce-last" hidefocus="1" tabindex="-1" role="group" style="border-width: 1px 0px 0px;"><div id="mceu_92-body" class="mce-container-body mce-flow-layout"><div id="mceu_93" class="mce-path mce-flow-layout-item mce-first"><div class="mce-path-item">&nbsp;</div></div><div id="mceu_94" class="mce-flow-layout-item mce-last mce-resizehandle"><i class="mce-ico mce-i-resize"></i></div></div></div></div></div><textarea class="wp-editor-area" rows="20" autocomplete="off" cols="40" name="rlt_additional_info" id="rlt_additional_info" style="display: none;" aria-hidden="true"></textarea></div>
                                                        <div class="uploader-editor">
                                                            <div class="uploader-editor-content">
                                                                <div class="uploader-editor-title">Drop files to upload</div>
                                                            </div>
                                                        </div></div>

                                                </td>
                                            </tr>
                                            <tr>
                                                <th><label for="rlt_agent_photo">Photo</label></th>
                                                <td>
                                                    <button class="rlt_add_agent_photo button" disabled="disabled">Add photo</button>
                                                </td>
                                            </tr>
                                            </tbody></table>
                                        <div id="rlt_agent_photo">
                                        </div>
                                        <div id="rlt_agent_add_photo" class="clear"></div>
                                        <div id="rlt_agent_delete_photo"></div>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                                <div id="slugdiv" class="postbox  hide-if-js" style="">
                                    <button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Toggle panel: Slug</span><span class="toggle-indicator" aria-hidden="true"></span></button><h2 class="hndle ui-sortable-handle"><span>Slug</span></h2>
                                    <div class="inside">
                                        <label class="screen-reader-text" for="post_name">Slug</label><input name="post_name" type="text" size="13" id="post_name" value="" disabled="disabled">
                                    </div>
                                </div>
                            </div><div id="advanced-sortables" class="meta-box-sortables ui-sortable"></div></div>
                    </div><!-- /post-body -->
                    <br class="clear">
                </div><!-- /poststuff -->
            </form>
    <?php }
}

if ( ! function_exists( 'rlt_features_block' ) ) {
    function rlt_features_block() { ?>
        <form class="search-form wp-clearfix" method="get">
            <p class="search-box">
                <label class="screen-reader-text" for="tag-search-input">Search Features:</label>
                <input type="search" id="tag-search-input" name="s" value="" disabled="disabled">
                <input type="submit" id="search-submit" class="button" value="Search Features" disabled="disabled"></p>
        </form>
        <div id="col-container" class="wp-clearfix">

            <div id="col-left" style="float: left;">
                <div class="col-wrap">


                    <div class="form-wrap">
                        <h2>Add New Feature</h2>
                        <form id="addtag" method="post" action="" class="validate">
                            <div class="form-field form-required term-name-wrap">
                                <label for="tag-name">Name</label>
                                <input name="tag-name" id="tag-name" type="text" value="" size="40" aria-required="true" disabled="disabled">
                                <p>The name is how it appears on your site.</p>
                            </div>
                            <div class="form-field term-slug-wrap">
                                <label for="tag-slug">Slug</label>
                                <input name="slug" id="tag-slug" type="text" value="" size="40" disabled="disabled">
                                <p>The “slug” is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.</p>
                            </div>
                            <div class="form-field term-description-wrap">
                                <label for="tag-description">Description</label>
                                <textarea name="description" id="tag-description" rows="5" cols="40" disabled="disabled"></textarea>
                                <p>The description is not prominent by default; however, some themes may show it.</p>
                            </div>

                            <p class="submit">
                                <input type="submit" name="submit" id="submit" class="button button-primary" value="Add New Feature" disabled="disabled">		<span class="spinner"></span>
                            </p>
                        </form></div>
                </div>
            </div><!-- /col-left -->

            <div id="col-right">
                <div class="col-wrap">


                    <form id="posts-filter" method="post">
                        <input type="hidden" name="taxonomy" value="feature">
                        <input type="hidden" name="post_type" value="property">

                        <input type="hidden" id="_wpnonce" name="_wpnonce" value="c4b23b882f"><input type="hidden" name="_wp_http_referer" value="/wp-admin/edit-tags.php?taxonomy=feature&amp;post_type=property">	<div class="tablenav top">

                            <div class="alignleft actions bulkactions">
                                <label for="bulk-action-selector-top" class="screen-reader-text">Select bulk action</label><select name="action" id="bulk-action-selector-top" disabled="disabled">
                                    <option value="-1">Bulk Actions</option>
                                    <option value="delete">Delete</option>
                                </select>
                                <input type="submit" id="doaction" class="button action" value="Apply" disabled="disabled">
                            </div>
                            <div class="tablenav-pages one-page"><span class="displaying-num">4 items</span>
                                <span class="pagination-links"><span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
<span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
<span class="paging-input"><label for="current-page-selector" class="screen-reader-text">Current Page</label><input class="current-page" id="current-page-selector" type="text" name="paged" value="1" size="1" aria-describedby="table-paging"><span class="tablenav-paging-text"> of <span class="total-pages">1</span></span></span>
<span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>
<span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span></span></div>
                            <br class="clear">
                        </div>
                        <h2 class="screen-reader-text">Features list</h2><table class="wp-list-table widefat fixed striped tags">
                            <thead>
                            <tr>
                                <td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">Select All</label><input id="cb-select-all-1" type="checkbox"></td><th scope="col" id="name" class="manage-column column-name column-primary sortable desc"><a href=""><span>Name</span><span class="sorting-indicator"></span></a></th><th scope="col" id="description" class="manage-column column-description sortable desc"><a href=""><span>Description</span><span class="sorting-indicator"></span></a></th><th scope="col" id="slug" class="manage-column column-slug sortable desc"><a href=""><span>Slug</span><span class="sorting-indicator"></span></a></th><th scope="col" id="posts" class="manage-column column-posts num sortable desc"><a href=""><span>Count</span><span class="sorting-indicator"></span></a></th>	</tr>
                            </thead>

                            <tbody id="the-list" data-wp-lists="list:tag">
                            <tr id="tag-29" class="level-0"><th scope="row" class="check-column"><label class="screen-reader-text" for="cb-select-29">Select Balcony</label><input type="checkbox" name="delete_tags[]" value="29" id="cb-select-29"></th><td class="name column-name has-row-actions column-primary" data-colname="Name"><strong><a class="row-title" href="" aria-label="“Balcony” (Edit)">Balcony</a></strong><br><div class="hidden" id="inline_29"><div class="name">Balcony</div><div class="slug">balcony</div><div class="parent">0</div></div><div class="row-actions"><span class="edit"><a href="" aria-label="Edit “Balcony”">Edit</a> | </span><span class="inline hide-if-no-js"><button type="button" class="button-link editinline" aria-label="Quick edit “Balcony” inline" aria-expanded="false">Quick&nbsp;Edit</button> | </span><span class="delete"><a href="" class="delete-tag aria-button-if-js" aria-label="Delete “Balcony”" role="button">Delete</a> | </span><span class="view"><a href="" aria-label="View “Balcony” archive">View</a></span></div><button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button></td><td class="description column-description" data-colname="Description"><span aria-hidden="true">—</span><span class="screen-reader-text">No description</span></td><td class="slug column-slug" data-colname="Slug">balcony</td><td class="posts column-posts" data-colname="Count"><a href="">4</a></td></tr><tr id="tag-30" class="level-0"><th scope="row" class="check-column"><label class="screen-reader-text" for="cb-select-30">Select Built-in Wardrobes</label><input type="checkbox" name="delete_tags[]" value="30" id="cb-select-30"></th><td class="name column-name has-row-actions column-primary" data-colname="Name"><strong><a class="row-title" href="" aria-label="“Built-in Wardrobes” (Edit)">Built-in Wardrobes</a></strong><br><div class="hidden" id="inline_30"><div class="name">Built-in Wardrobes</div><div class="slug">built-in-wardrobes</div><div class="parent">0</div></div><div class="row-actions"><span class="edit"><a href="" aria-label="Edit “Built-in Wardrobes”">Edit</a> | </span><span class="inline hide-if-no-js"><button type="button" class="button-link editinline" aria-label="Quick edit “Built-in Wardrobes” inline" aria-expanded="false">Quick&nbsp;Edit</button> | </span><span class="delete"><a href="" class="delete-tag aria-button-if-js" aria-label="Delete “Built-in Wardrobes”" role="button">Delete</a> | </span><span class="view"><a href="" aria-label="View “Built-in Wardrobes” archive">View</a></span></div><button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button></td><td class="description column-description" data-colname="Description"><span aria-hidden="true">—</span><span class="screen-reader-text">No description</span></td><td class="slug column-slug" data-colname="Slug">built-in-wardrobes</td><td class="posts column-posts" data-colname="Count"><a href="">18</a></td></tr><tr id="tag-40" class="level-0"><th scope="row" class="check-column"><label class="screen-reader-text" for="cb-select-40">Select Central Air</label><input type="checkbox" name="delete_tags[]" value="40" id="cb-select-40"></th><td class="name column-name has-row-actions column-primary" data-colname="Name"><strong><a class="row-title" href="" aria-label="“Central Air” (Edit)">Central Air</a></strong><br><div class="hidden" id="inline_40"><div class="name">Central Air</div><div class="slug">central-air</div><div class="parent">0</div></div><div class="row-actions"><span class="edit"><a href="" aria-label="Edit “Central Air”">Edit</a> | </span><span class="inline hide-if-no-js"><button type="button" class="button-link editinline" aria-label="Quick edit “Central Air” inline" aria-expanded="false">Quick&nbsp;Edit</button> | </span><span class="delete"><a href="" class="delete-tag aria-button-if-js" aria-label="Delete “Central Air”" role="button">Delete</a> | </span><span class="view"><a href="" aria-label="View “Central Air” archive">View</a></span></div><button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button></td><td class="description column-description" data-colname="Description"><span aria-hidden="true">—</span><span class="screen-reader-text">No description</span></td><td class="slug column-slug" data-colname="Slug">central-air</td><td class="posts column-posts" data-colname="Count"><a href="">5</a></td></tr><tr id="tag-31" class="level-0"><th scope="row" class="check-column"><label class="screen-reader-text" for="cb-select-31">Select Dishwasher</label><input type="checkbox" name="delete_tags[]" value="31" id="cb-select-31"></th><td class="name column-name has-row-actions column-primary" data-colname="Name"><strong><a class="row-title" href="" aria-label="“Dishwasher” (Edit)">Dishwasher</a></strong><br><div class="hidden" id="inline_31"><div class="name">Dishwasher</div><div class="slug">dishwasher</div><div class="parent">0</div></div><div class="row-actions"><span class="edit"><a href="" aria-label="Edit “Dishwasher”">Edit</a> | </span><span class="inline hide-if-no-js"><button type="button" class="button-link editinline" aria-label="Quick edit “Dishwasher” inline" aria-expanded="false">Quick&nbsp;Edit</button> | </span><span class="delete"><a href="" class="delete-tag aria-button-if-js" aria-label="Delete “Dishwasher”" role="button">Delete</a> | </span><span class="view"><a href="" aria-label="View “Dishwasher” archive">View</a></span></div><button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button></td><td class="description column-description" data-colname="Description"><span aria-hidden="true">—</span><span class="screen-reader-text">No description</span></td><td class="slug column-slug" data-colname="Slug">dishwasher</td><td class="posts column-posts" data-colname="Count"><a href="">18</a></td></tr>	</tbody>

                            <tfoot>
                            <tr>
                                <td class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-2">Select All</label><input id="cb-select-all-2" type="checkbox"></td><th scope="col" class="manage-column column-name column-primary sortable desc"><a href=""><span>Name</span><span class="sorting-indicator"></span></a></th><th scope="col" class="manage-column column-description sortable desc"><a href=""><span>Description</span><span class="sorting-indicator"></span></a></th><th scope="col" class="manage-column column-slug sortable desc"><a href=""><span>Slug</span><span class="sorting-indicator"></span></a></th><th scope="col" class="manage-column column-posts num sortable desc"><a href=""><span>Count</span><span class="sorting-indicator"></span></a></th>	</tr>
                            </tfoot>

                        </table>
                        <div class="tablenav bottom">

                            <div class="alignleft actions bulkactions">
                                <label for="bulk-action-selector-bottom" class="screen-reader-text">Select bulk action</label><select name="action2" id="bulk-action-selector-bottom" disabled="disabled">
                                    <option value="-1">Bulk Actions</option>
                                    <option value="delete">Delete</option>
                                </select>
                                <input type="submit" id="doaction2" class="button action" value="Apply" disabled="disabled">
                            </div>
                            <div class="tablenav-pages one-page"><span class="displaying-num">4 items</span>
                                <span class="pagination-links"><span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
<span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
<span class="screen-reader-text">Current Page</span><span id="table-paging" class="paging-input"><span class="tablenav-paging-text">1 of <span class="total-pages">1</span></span></span>
<span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>
<span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span></span></div>
                            <br class="clear">
                        </div>

                    </form>

                </div>
            </div><!-- /col-right -->

        </div>
    <?php }
}