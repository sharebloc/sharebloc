<!DOCTYPE html>
<html>
    <head>
        <title>ShareBloc - Every Sale is a Space Race: Top 40 Sales Vendors.</title>
        <meta name="keywords" content="business content, content discovery, enterprise, b2b, SMB, small medium business, lead-gen"/>
        <meta name="description" content="ShareBloc is a community of like-minded professionals who share, curate and discuss business content that matters.">
        <meta http-equiv="content-type" content="text/html; charset=utf-8">
        <link rel="shortcut icon" href="/favicon.png" type="image/x-icon">
        <link rel="stylesheet" href="css/infographics/splash-space.css" type="text/css" />
    </head>
    <body>
        <div class="download_block">
            <div class="download_link_div fixed">
                <div class="download_block_content">
                    <a href="{$index_page}"><img class="vslogo_header" alt="ShareBloc" src="/images/sharebloc_logo.png"></a>
                    <a class="download_link fixed" href="/files/Every_Sale_is_a_Space_Race.pdf">Download the PDF</a>

                    {if $logged_in}
                        <div id="user_menu_content" class="header_content">
                            <div class="header_person" id="username_link">
                                <img class="icon_header" src="{if isset($user_info.logo_hash)}/logos/{$user_info.logo_hash}_thumb.jpg{else}/images/nophoto.png{/if}">
                                <a class="header_link" href="/users/{$user_info.code_name}">{$user_info.first_name}</a>
                            </div>
                            <div id="user_menu" class="hidden">
                                <a class="header_submenu" href="/users/{$user_info.code_name}">My Account</a><br>
                                {if $is_admin}
                                    <a class="header_submenu" href="/admin/">Admin</a><br>
                                {/if}
                                <a class="header_submenu" href="/logout.php">Logout</a>
                            </div>
                        </div>
                    {else}
                        <div id="login_links" class="header_content">
                            <div class="header_login">
                                <a id="login_link" class="header_link" href="{$login_redir_path}">Sign In</a>
                            </div>
                            {if !isset($front)}
                                <div class="header_signup">
                                    <a class="header_link" href="{$join_redir_path}">Join</a>
                                </div>
                            {/if}
                        </div>
                    {/if}
                </div>
            </div>
        </div>
        <div class="color_block_container">
            <div class="color_block_content">
                {* header *}
                <div class="color_header">
                    <div class="top_left_image">
                        <img src="/images/splash/space_race_left.png">
                    </div>
                    <div class="color_header_text_div">
                        <div class="color_header_title">HOW TO BEAT YOUR COMPETITION TO THE CLOSE</div>
                        <div class="color_header_title_text fleft">When you’re chasing down a sale, you are probably selling against a competitor. While sales is not nearly as dramatic as the space race, landing a big customer could make you feel over the moon.</div>
                        <div class="color_header_title_text fleft">We identified 40 enterprise vendors that help sales people better close their leads. There are three major tools sales people can use (analytics, presentation, closing) and all those tools should tie into your CRM platform.</div>
                        <div class="clear"></div>
                    </div>
                    <div class="top_right_image"><img src="/images/splash/rockets_right.png"></div>
                    <div class="clear"></div>
                    <div class="vslogo_div">
                        <a href="/">
                            <img class="vslogo" src="/images/vendorstack_logo_grey.png" alt="ShareBloc">
                        </a>
                    </div>
                </div>

                <div class="color_center">
                    {* left column *}
                    <div class="color_block">
                        <div class="color_block_img scroll_link" data-chid="analytics">
                            <img src="/images/splash/analitycs.png">
                        </div>
                        <div class="color_block_descr">
                            Just like the dials and gauges tell you what’s going on with your launch, analytics tell you how your lead is engaging with you and your product. The following vendors can give you more context on the hottest leads in your funnel.
                        </div>
                        <div class="color_block_protip">
                            <div class="color_block_protip_word fleft"><span class="pro_part">PRO</span><br><span class="tip_part">TIP</span></div>
                            <div class="color_block_protip_text fleft">"As marketing has become a revenue function, the number of tools and data volume have proliferated. Revenue-centric marketers must focus on uncovering insights from their CRM, marketing automation, social media and other systems."
                                <br>
                                &mdash;Nadim Hossain, CEO, <a href="{$base_url}/companies/brightfunnel">Brightfunnel</a></div>
                            <div class="clear"></div>
                        </div>

                        <div class="group_block">
                            <div class="vendors_column gen_purp_vendors">
                                <div class="vendor_group_title scroll_link" data-chid="{$sale_space_info.0.vendor_groups.0.scroll_id}">{$sale_space_info.0.vendor_groups.0.title}</div>
                                {foreach from=$sale_space_info.0.vendor_groups.0.vendors key=id item=v}
                                    <div class="vendors_block">
                                        <div class="fleft">
                                            <a href="/companies/{$v.code_name}">
                                                <img data-codeName="{$v.code_name}" class="vendor_icon"  alt="{$v.vendor_name}" {if $v.local}src="/logos/{$v.logo_hash}_thumb.jpg"{else}src="{$base_url}/logos/{$v.logo_hash}_thumb.jpg"{/if}/>
                                            </a>
                                        </div>
                                    </div>
                                {/foreach}
                            </div>
                            <div class="vendors_column sales_spec_vendors">
                                <div class="vendor_group_title scroll_link" data-chid="{$sale_space_info.0.vendor_groups.1.scroll_id}">{$sale_space_info.0.vendor_groups.1.title}</div>
                                {foreach from=$sale_space_info.0.vendor_groups.1.vendors key=id item=v}
                                    <div class="vendors_block">
                                        <div class="fleft">
                                            <a href="/companies/{$v.code_name}">
                                                <img data-codeName="{$v.code_name}" class="vendor_icon"  alt="{$v.vendor_name}" {if $v.local}src="/logos/{$v.logo_hash}_thumb.jpg"{else}src="{$base_url}/logos/{$v.logo_hash}_thumb.jpg"{/if}/>
                                            </a>
                                        </div>
                                    </div>
                                {/foreach}
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                    {* center column *}
                    <div class="color_block">
                        <div class="color_block_img scroll_link" data-chid="presentation">
                            <img src="/images/splash/presentation.png">
                        </div>
                        <div class="color_block_descr">
                            You need to know how your space flight is doing or you’ll be flying blind. The following vendors better prepare you during your sales pitch so you can track and measure how effective your sales presentation is going.
                        </div>
                        <div class="color_block_protip">
                            <div class="color_block_protip_word fleft"><span class="pro_part">PRO</span><br><span class="tip_part">TIP</span></div>
                            <div class="color_block_protip_text fleft">
                                "Inside sales communications occur over the phone, over the internet or via emailed documentation. While this allows for a more cost effective sale, it also opens up a knowledge gap for the seller. It is vital to be able to track the level of interest of prospective customers so a sales team knows if they should follow up, when to follow up and what to follow up with."
                                <br>
                                &mdash;Khuram Hussain, CEO, <a href="{$base_url}/companies/fileboard">Fileboard</a></div>
                            <div class="clear"></div>
                        </div>

                        <div class="group_block">
                            <div class="vendors_column gen_purp_present_vendors">
                                <div class="vendor_group_title scroll_link" data-chid="{$sale_space_info.1.vendor_groups.0.scroll_id}">{$sale_space_info.1.vendor_groups.0.title}</div>
                                {foreach from=$sale_space_info.1.vendor_groups.0.vendors key=id item=v}
                                    <div class="vendors_block">
                                        <div class="fleft">
                                            <a href="/companies/{$v.code_name}">
                                                <img data-codeName="{$v.code_name}" class="vendor_icon"  alt="{$v.vendor_name}" {if $v.local}src="/logos/{$v.logo_hash}_thumb.jpg"{else}src="{$base_url}/logos/{$v.logo_hash}_thumb.jpg"{/if}/>
                                            </a>
                                        </div>
                                    </div>
                                {/foreach}
                            </div>
                            <div class="vendors_column sales_spec_present_vendors">
                                <div class="vendor_group_title scroll_link" data-chid="{$sale_space_info.1.vendor_groups.1.scroll_id}">{$sale_space_info.1.vendor_groups.1.title}</div>
                                {foreach from=$sale_space_info.1.vendor_groups.1.vendors key=id item=v}
                                    <div class="vendors_block">
                                        <div class="fleft">
                                            <a href="/companies/{$v.code_name}">
                                                <img data-codeName="{$v.code_name}" class="vendor_icon"  alt="{$v.vendor_name}" {if $v.local}src="/logos/{$v.logo_hash}_thumb.jpg"{else}src="{$base_url}/logos/{$v.logo_hash}_thumb.jpg"{/if}/>
                                            </a>
                                        </div>
                                    </div>
                                {/foreach}
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                    {* right column *}
                    <div class="color_block">
                        <div class="color_block_img scroll_link" data-chid="closing">
                            <img src="/images/splash/closing.png">
                        </div>
                        <div class="color_block_descr">
                            If you do a moon shot, be sure to stick the landing. Here are a few of the top vendors that help you close your leads when they’re ready to sign.
                        </div>
                        <div class="color_block_protip">
                            <div class="color_block_protip_word fleft"><span class="pro_part">PRO</span><br><span class="tip_part">TIP</span></div>
                            <div class="color_block_protip_text fleft">
                                "Make signing the final contract as easy as possible for your client. Setup common sales documents as templates and fill in the client's details for them them so all they have to do is e-Sign. This will lead to fewer errors, less back and forth, and faster closings."
                                <br>
                                &mdash;Neal O’Mara, CTO, <a href="{$base_url}/companies/hellosign">HelloSign</a>
                            </div>
                            <div class="clear"></div>
                        </div>


                        <div class="group_block">
                            <div class="vendors_column e_sign_vendors">
                                <div class="vendor_group_title scroll_link" data-chid="{$sale_space_info.2.vendor_groups.0.scroll_id}">{$sale_space_info.2.vendor_groups.0.title}</div>
                                {foreach from=$sale_space_info.2.vendor_groups.0.vendors key=id item=v}
                                    <div class="vendors_block">
                                        <div class="fleft">
                                            <a href="/companies/{$v.code_name}">
                                                <img data-codeName="{$v.code_name}" class="vendor_icon"  alt="{$v.vendor_name}" {if $v.local}src="/logos/{$v.logo_hash}_thumb.jpg"{else}src="{$base_url}/logos/{$v.logo_hash}_thumb.jpg"{/if}/>
                                            </a>
                                        </div>
                                    </div>
                                {/foreach}
                            </div>

                            <div class="clear"></div>
                        </div>
                    </div>
                    <br class="clear">
                </div>
                {* footer *}
                <div class="color_footer">
                    <div class="color_footer_left fleft">
                        <div class="color_block_descr fleft">
                            <div class="color_footer_crm_badge">
                                <div class="crm_left_title">CRM</div>
                                <div class="crm_right_title">
                                    CUSTOMER RELATIONSHIP MANAGEMENT
                                </div>
                            </div>
                            <div class="fleft footer_descr">
                                All this can’t be done without the mission control of sales, the customer relationship management (CRM) system. All modern CRM systems layer on the best applications (analytics, presentation and closing) into their platform. Here are the vendors you should consider as you’re tracking your sales through the funnel.
                            </div>
                        </div>
                        <div class="clear"></div>
                        <div class="color_block_protip">
                            <div class="color_block_protip_word fleft"><span class="pro_part">PRO</span><br><span class="tip_part">TIP</span></div>
                            <div class="color_block_protip_text fleft">
                                "Sales teams amass huge quantities of data—more than can be tracked manually in a traditional CRM. RelateIQ automatically aggregates data from email, calendars, social networks, and phone calls, entering data for you while you and your team work. Not only does this eliminate manual data entry, but also allows you to mine your own data for key insights, such as who needs your attention right now and where you are in danger of dropping the ball next week."
                                <br>
                                &mdash;Steve Loughlin, Marketing, <a href="{$base_url}/companies/relateiq">RelateIQ</a>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                    <div class="color_footer_right fleft">
                        <div class="color_block_footer_img_div fleft">
                            <img class="color_block_footer_img" src="/images/splash/footer_rocket.png">
                            <img class="color_block_footer_img_btns" src="/images/splash/buttons.png">
                        </div>
                        <div class="color_block_footer_img_div fleft">
                            <img class="color_block_footer_img" src="/images/splash/footer_flight.png">
                            <img class="color_block_footer_img_btns" src="/images/splash/buttons.png">
                        </div>
                        <div class="color_block_footer_img_div fleft">
                            <img class="color_block_footer_img" src="/images/splash/footer_moon.png">
                            <img class="color_block_footer_img_btns" src="/images/splash/buttons.png">
                        </div>
                        <div class="clear"></div>
                        <div class="group_block">
                            <div class="vendors_column traditional_vendors">
                                <div class="vendor_group_title scroll_link" data-chid="{$sale_space_info.3.vendor_groups.0.scroll_id}">{$sale_space_info.3.vendor_groups.0.title}</div>
                                {foreach from=$sale_space_info.3.vendor_groups.0.vendors key=id item=v}
                                    <div class="vendors_block">
                                        <div class="fleft">
                                            <a href="/companies/{$v.code_name}">
                                                <img data-codeName="{$v.code_name}" class="vendor_icon"  alt="{$v.vendor_name}" {if $v.local}src="/logos/{$v.logo_hash}_thumb.jpg"{else}src="{$base_url}/logos/{$v.logo_hash}_thumb.jpg"{/if}/>
                                            </a>
                                        </div>
                                    </div>
                                {/foreach}
                            </div>
                            <div class="vendors_column data_driven_vendors">
                                <div class="vendor_group_title scroll_link" data-chid="{$sale_space_info.3.vendor_groups.1.scroll_id}">{$sale_space_info.3.vendor_groups.1.title}</div>
                                {foreach from=$sale_space_info.3.vendor_groups.1.vendors key=id item=v}
                                    <div class="vendors_block">
                                        <div class="fleft">
                                            <a href="/companies/{$v.code_name}">
                                                <img data-codeName="{$v.code_name}" class="vendor_icon"  alt="{$v.vendor_name}" {if $v.local}src="/logos/{$v.logo_hash}_thumb.jpg"{else}src="{$base_url}/logos/{$v.logo_hash}_thumb.jpg"{/if}/>
                                            </a>
                                        </div>
                                    </div>
                                {/foreach}
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div> {* color footer end *}
            </div>
        </div>
        <div class="text_block_container">
            <div class="infographic_title_content">
                <div class="content_header_left_div"><img src="/images/splash/space_race_planet.png"></div>
                <div class="content_header_center_div">HOW TO BEAT<br>YOUR COMPETITION<br>TO THE CLOSE</div>
                <div class="content_header_right_div"><img src="/images/splash/space_race_rockets.png"></div>
            </div>
            <br>
            <div class="infographic_block_content" id="chapter_title_textstart">
                {* ADDENDUM *}
                <div>
                    <div class="part_title">ADDENDUM</div>
                    <div class="part_intro_text">
                        <p>When you’re chasing down a sale, you are probably selling against a competitor. While sales is not nearly as dramatic as the space race, landing a big customer could make you feel over the moon.
                        <p>We identified 40 enterprise vendors that help sales people better close their leads. There are three major tools sales people can use (analytics, presentation, closing) and all those tools should tie into your CRM platform.
                    </div>

                    <div class="chapter_title">
                        WHO ACTUALLY USES THIS STUFF?
                    </div>

                    <div class="chapter_content">
                        <div class="chapter_text">
                            <p>When we interviewed our sales experts, the resounding feedback was to get out of the salesperson’s way. After all, the role of the salesperson is to close the deal. If that’s the case, who is the one actually making the purchasing decision for all these new sales tools?
                            <p>It turns out sales managers are now working more closely with their marketing counterparts, particularly in light of all the new sales and marketing tools that bring marketing qualified leads down to the sales funnel.
                            <p>Jakob Thusgaard, CEO of <a href="{$base_url}/companies/yoursales">YourSales</a>, observes:
                        </div>

                        <div class="chapter_protip">
                            <div class="chapter_protip_quotes">	&ldquo;</div>
                            <div class="chapter_protip_text">
                                <p>Globalization and information availability have shifted power in the buyer-seller relationship towards the buyer. This evolution makes it more critical than ever for sales professionals to engage with the customer as early in the buying cycle as possible. It's that - or face lost deals and heavy discounting.
                            </div>
                        </div>

                        <div class="chapter_text">
                            <p>With power shifting towards the buyer, it becomes even more important to get closer into the community before the buyer is sales-qualified. Does that mean sales teams are becoming more marketing-like in some instances?
                            <p>Lori Richardson, Founder and CEO, <a href="{$base_url}/companies/score_more_sales">Score More Sales</a> has her take:
                        </div>

                        <div class="chapter_protip">
                            <div class="chapter_protip_quotes">	&ldquo;</div>
                            <div class="chapter_protip_text">
                                <p>People search for a solution and narrow down their choices without input from vendors. You don’t know all the opportunities you are missing. To get a sale you must collaborate with the community to get new customers.
                            </div>
                        </div>

                        <div class="chapter_text">
                            <p>This doesn’t necessarily mean all salespeople are marketers and vice versa. Rather, the value of having a data-driven marketing and sales funnel is to identify the best prospects early in the process, so the marketers know right when to bring in the sales rep.
                            <p>Jamie Grenney, VP of Marketing, Infer, maybe sums it best:
                        </div>

                        <div class="chapter_protip">
                            <div class="chapter_protip_quotes">	&ldquo;</div>
                            <div class="chapter_protip_text">
                                <p>Over the last 10 years Sales Automation and Marketing Automation have done a terrific job of structuring the funnel and capturing transactional data. But what percentage of your time is spent with prospects that don't convert? 50%? Maybe more? And how often do you give up on a prospect one touch too soon?
                                <p>Forward thinking companies are beginning to look to predictive intelligence to help them identify which leads are most likely to convert and which will have the biggest revenue impact. This helps eliminate wasted energy and it allows you to focus your resources where you've got the best shot at winning. There are also cases where predictive intelligence is highlighting new opportunities the company might not have otherwise seen.
                            </div>
                        </div>

                    </div>
                </div>

                {* ANALYTICS *}
                <div id="chapter_analytics">
                    <div class="part_title" id="chapter_title_analytics">
                        ANALYTICS
                    </div>
                    <div class="part_intro_text">
                        <p>Just like the dials and gauges tell you what’s going on with your launch, analytics tell you how your lead is engaging with you and your product. The following vendors can give you more context on the hottest leads in your funnel.
                    </div>

                    {* GENERAL-PURPOSE ANALITYCS *}
                    <div class="chapter_title" id="chapter_title_{$sale_space_info.0.vendor_groups.0.scroll_id}">
                        GENERAL-PURPOSE ANALITYCS
                    </div>
                    <div class="chapter_content">

                        <div class="chapter_vendors">
                            {foreach from=$sale_space_info.0.vendor_groups.0.vendors key=v_id item=v}
                                <div class="chapter_vendor_block fleft">
                                    <div class="chapter_vendor_logo">
                                        <a href="/companies/{$v.code_name}">
                                            <img data-codeName="{$v.code_name}" class="chapter_vendor_icon"  alt="{$v.vendor_name}" {if $v.local}src="/logos/{$v.logo_hash}_thumb.jpg"{else}src="{$base_url}/logos/{$v.logo_hash}_thumb.jpg"{/if}/>
                                        </a>
                                    </div>
                                    <div class="chapter_vendor_name">{$v.vendor_name|upper}</div>
                                </div>
                            {/foreach}
                            <div class="clear"></div>
                        </div>
                    </div>
                    {* SALES-SPECIFIC *}
                    <div class="chapter_title" id="chapter_title_{$sale_space_info.0.vendor_groups.1.scroll_id}">
                        SALES-SPECIFIC
                    </div>
                    <div class="chapter_content">

                        <div class="chapter_vendors">
                            {foreach from=$sale_space_info.0.vendor_groups.1.vendors key=v_id item=v}
                                <div class="chapter_vendor_block fleft">
                                    <div class="chapter_vendor_logo">
                                        <a href="/companies/{$v.code_name}">
                                            <img data-codeName="{$v.code_name}" class="chapter_vendor_icon"  alt="{$v.vendor_name}" {if $v.local}src="/logos/{$v.logo_hash}_thumb.jpg"{else}src="{$base_url}/logos/{$v.logo_hash}_thumb.jpg"{/if}/>
                                        </a>
                                    </div>
                                    <div class="chapter_vendor_name">{$v.vendor_name|upper}</div>
                                </div>
                            {/foreach}
                            <div class="clear"></div>
                        </div>
                    </div>

                    <div class="chapter_text">
                        <p>There are a lot of sales analytics and intelligence vendors out there. Small and medium sized businesses now have access to data tools that were only available to the largest enterprises. But just because you have the data on a dashboard, it doesn’t mean you’ve solved your sales intelligence problems. You have to close the loop and make the data actionable, which in the sales funnel, means top-line growth. Here are a few vendors we like in this space.
                        <p><a href="{$base_url}/companies/domo">Domo</a> brings together siloed data into one place. It emphasizes business needs and having data in one place. Its sales solution brings all your important metrics into one place especially ones like pipeline velocity and what percentage of deals do you win and their profitability. <a href="{$base_url}/companies/domo">Domo</a> suggests <a href="http://www.domo.com/blog/2012/05/5-moneyball-metrics-sales-executives-cant-ignore/">using metrics</a> such as pipeline velocity, winning percentages, closing speed and profitability of deals closed to measure your sales reps. Of course without integrations of other systems, calculating these metrics wouldn’t be possible. <a href="{$base_url}/companies/domo">Domo</a> clients include <a href="{$base_url}/companies/goodwill">Goodwill</a>, <a href="{$base_url}/companies/nissan">Nissan</a> and <a href="{$base_url}/companies/xerox">Xerox</a>.
                        <p><a href="{$base_url}/companies/cloudamp">CloudAmp</a> leverages the enterprise features of <a href="{$base_url}/companies/google_analytics">Google Analytics</a> and mashes it with your <a href="{$base_url}/companies/salesforcecom">Salesforce</a> app, closing the loop on how users traffic and engage your site to lead nurturing and conversion. With <a href="{$base_url}/companies/cloudamp">CloudAmp‘s</a> Campaign Tracker for Google Adwords and Analytics, you can track and tag URLs, searches, etc. You can also track Google Adwords into <a href="{$base_url}/companies/salesforcecom">Salesforce</a>, <a href="http://blog.vendorstack.com/2013/06/13/5-options-to-replace-salesforce-for-google-adwords/">a nice alternative to Salesforce for Google Adwords</a>. Their <a href="{$base_url}/companies/cloudamp">CloudAmp</a> Analytics Dashboards imports Google analytics data into prebuilt dashboards and reports in <a href="{$base_url}/companies/salesforcecom">Salesforce</a>. Customers of <a href="{$base_url}/companies/cloudamp">CloudAmp</a> include <a href="{$base_url}/companies/rackspace">Rackspace</a>, <a href="{$base_url}/companies/ironio">Iron.io</a> and <a href="{$base_url}/companies/chartio">Chart.io</a>.
                    </div>

                    <div class="vendor_story_block">
                        <div class="vendor_story_logo"><img class="" src="/images/splash/glass.png"></div>
                        <div class="vendor_story_title">
                            <span class="case_study_title">CASE STUDY:
                                <a class="vendor_story_link" href="{$base_url}/companies/brightfunnel">BRIGHTFUNNEL</a>&nbsp;&nbsp;
                            </span>
                            <a href="{$base_url}/companies/brightfunnel">
                                <img class="vendor_logo_story" src="{$base_url}/logos/brightfunnel_v_thumb.jpg">
                            </a>
                        </div>
                        <div class="vendor_story_text">
                            <p>BrightFunnel is "Splunk for CMOs": we connect the dots between marketing data silos to generate predictive, actionable revenue insights. For the first time, CMOs can get a quick handle on what’s happening in their funnel, why, and what to do about it.
                        </div>
                        <div class="vendor_story_quote">
                            <p>&ldquo;As marketing has become a revenue function, the numbers of tools and data volume have proliferated. Revenue-centric marketers must focus on uncovering insights from their CRM, marketing automation, social media and other systems.&rdquo;
                        </div>
                        <span class="vendor_story_author">&mdash;Nadim Hossain, CEO, <a class="vendor_story_link" href="{$base_url}/companies/brightfunnel" >Brightfunnel</a></span>
                    </div>


                    <div class="vendor_story_block">
                        <div class="vendor_story_logo"><img class="" src="/images/splash/glass.png"></div>
                        <div class="vendor_story_title">
                            <span class="case_study_title">CASE STUDY:
                                <a class="vendor_story_link" href="{$base_url}/companies/funnelfire" >FUNNELFIRE</a>&nbsp;&nbsp;
                            </span>
                            <a href="{$base_url}/companies/funnelfire">
                                <img class="vendor_logo_story" src="{$base_url}/logos/funnelfire_v_thumb.jpg">
                            </a>
                        </div>
                        <div class="vendor_story_text">
                            <p>FunnelFire combs the Web for relevant data on people and companies and delivers research to you in a pro-active way, empowering you to know your prospects and customers even better - while saving hours of research time.
                        </div>
                        <div class="vendor_story_quote">
                            <p>&ldquo;Information is available more than ever before. Customers are more prepared - having completed up to 60% of the sales process before talking to a sales rep. For this reason, sales has changed forever. Salespeople need to be more prepared, and have all the same information that prospects have. Unless salespeople  do this, they will lose the sale.&rdquo;
                        </div>
                        <span class="vendor_story_author">&mdash;Mark LaRosa, CEO, <a class="vendor_story_link" href="{$base_url}/companies/funnelfire" >Funnelfire</a></span>
                    </div>
                </div>

                {* PRESENTATION *}
                <div id="chapter_title_presentation">
                    <div class="part_title">PRESENTATION</div>
                    <div class="part_intro_text">
                        <p>You need to know how your space flight is doing or you’ll be flying blind. The following vendors better prepare you during your sales pitch so you can track and measure how effective your sales presentation is going.
                    </div>

                    {* GENERAL-PURPOSE PRESENTATION *}
                    <div class="chapter_title" id="chapter_title_{$sale_space_info.1.vendor_groups.0.scroll_id}">
                        GENERAL-PURPOSE PRESENTATION
                    </div>
                    <div class="chapter_content">

                        <div class="chapter_vendors">
                            {foreach from=$sale_space_info.1.vendor_groups.0.vendors key=v_id item=v}
                                <div class="chapter_vendor_block fleft">
                                    <div class="chapter_vendor_logo">
                                        <a href="/companies/{$v.code_name}">
                                            <img data-codeName="{$v.code_name}" class="chapter_vendor_icon"  alt="{$v.vendor_name}" {if $v.local}src="/logos/{$v.logo_hash}_thumb.jpg"{else}src="{$base_url}/logos/{$v.logo_hash}_thumb.jpg"{/if}/>
                                        </a>
                                    </div>
                                    <div class="chapter_vendor_name">{$v.vendor_name|upper}</div>
                                </div>
                            {/foreach}
                            <div class="clear"></div>
                        </div>
                    </div>
                    {* SALES-SPECIFIC *}
                    <div class="chapter_title" id="chapter_title_{$sale_space_info.1.vendor_groups.1.scroll_id}">
                        SALES-SPECIFIC
                    </div>
                    <div class="chapter_content">

                        <div class="chapter_vendors">
                            {foreach from=$sale_space_info.1.vendor_groups.1.vendors key=v_id item=v}
                                <div class="chapter_vendor_block fleft">
                                    <div class="chapter_vendor_logo">
                                        <a href="/companies/{$v.code_name}">
                                            <img data-codeName="{$v.code_name}" class="chapter_vendor_icon"  alt="{$v.vendor_name}" {if $v.local}src="/logos/{$v.logo_hash}_thumb.jpg"{else}src="{$base_url}/logos/{$v.logo_hash}_thumb.jpg"{/if}/>
                                        </a>
                                    </div>
                                    <div class="chapter_vendor_name">{$v.vendor_name|upper}</div>
                                </div>
                            {/foreach}
                            <div class="clear"></div>
                        </div>
                    </div>

                    <div class="chapter_text">
                        <p>A good presentation tool goes beyond an emailed power point slide. We like a few vendors in this space because they add a new dimension to the sales presentation.
                        <p><a href="{$base_url}/companies/fileboard">Fileboard</a> takes the mystery out of "is the other person paying attention?" in web presentations. With <a href="{$base_url}/companies/fileboard">Fileboard</a>, sales people send a <a href="{$base_url}/companies/fileboard">Fileboard</a> link to the recipient and can automatically <a href="http://www.fileboard.com/learn-more/">track activity</a> in the presentation, including time spent per slide and its viral factor within the organization. With better analytics, sales organizations can A/B test slide decks and check what the top sales people are doing with their pitches. Because all this activity is also automatically logged on your CRM, there’s no redundant data entry.
                    </div>

                    <div class="vendor_story_block">
                        <div class="vendor_story_logo"><img class="" src="/images/splash/glass.png"></div>
                        <div class="vendor_story_title">
                            <span class="case_study_title">CASE STUDY:
                                <a class="vendor_story_link" href="{$base_url}/companies/fileboard" >FILEBOARD</a>&nbsp;&nbsp;
                            </span>
                            <a href="{$base_url}/companies/fileboard">
                                <img class="vendor_logo_story" src="{$base_url}/logos/fileboard_v_thumb.jpg">
                            </a>
                        </div>
                        <div class="vendor_story_text">
                            <p>Sales people close more business by using Fileboard with their CRM system. Get out of the world of darkness and see what your leads and prospects are doing with your Sales collateral. Marketing and Sales teams love Fileboard because it helps them manage their collateral through out the Sales process.
                        </div>
                        <div class="vendor_story_quote">
                            <p>&ldquo;Inside sales communications occur over the phone, over the internet or via emailed documentation. While this allows for a more cost effective sale, it also opens up a knowledge gap for the seller. It is vital to be able to track the level of interest of prospective customers so a sales team knows if they should follow up, when to follow up and what to follow up with.&rdquo;
                        </div>
                        <span class="vendor_story_author">&mdash;Khuram Hussain, CEO, <a class="vendor_story_link" href="{$base_url}/companies/fileboard" >Fileboard</a></span>
                    </div>

                    <div class="chapter_text">
                        <p><a href="{$base_url}/companies/storydesk">StoryDesk</a> is an iPad-first presentation tool that leverages the form-factor and functionality of tablets. Using one of multiple pre-built presentation frameworks, customers can drag-and-drop pictures, text, video, audio and most forms of rich media documents to build an iPad friendly presentation. Salespeople can tap to expand and swipe to move on, giving the presentation a more tactile feel compared to power point. Like all good next-generation presentation tools, data is being collected in real-time so sales teams can see which slides/presentations work best with clients. Customers for <a href="{$base_url}/companies/storydesk">StoryDesk</a> include <a href="{$base_url}/companies/bbc">BBC</a>, <a href="{$base_url}/companies/3m">3M</a> and <a href="{$base_url}/companies/cargill">Cargill</a>.
                    </div>
                </div>

                {* CLOSING *}
                <div id="chapter_title_closing">
                    <div class="part_title">CLOSING</div>
                    <div class="part_intro_text">
                        <p>If you do a moon shot, be sure to stick the landing. Here are a few of the top vendors that help you close your leads when they’re ready to sign.
                    </div>

                    {* eSIGNATURE *}
                    <div class="chapter_title" id="chapter_title_{$sale_space_info.2.vendor_groups.0.scroll_id}">
                        eSIGNATURE
                    </div>
                    <div class="chapter_content">

                        <div class="chapter_vendors">
                            {foreach from=$sale_space_info.2.vendor_groups.0.vendors key=v_id item=v}
                                <div class="chapter_vendor_block fleft">
                                    <div class="chapter_vendor_logo">
                                        <a href="/companies/{$v.code_name}">
                                            <img data-codeName="{$v.code_name}" class="chapter_vendor_icon"  alt="{$v.vendor_name}" {if $v.local}src="/logos/{$v.logo_hash}_thumb.jpg"{else}src="{$base_url}/logos/{$v.logo_hash}_thumb.jpg"{/if}/>
                                        </a>
                                    </div>
                                    <div class="chapter_vendor_name">{$v.vendor_name|upper}</div>
                                </div>
                            {/foreach}
                            <div class="clear"></div>
                        </div>
                    </div>

                    <div class="chapter_text">
                        <p>For the most part, all modern eSignature vendors provide easy-to-use cloud-based or mobile-based eSignature functionality. Each of them should also provide automated tracking and filing, enterprise grade security and robust administrative controls for dealing with multiple clients and various types of documents. We like two vendors especially in this category.
                        <p>We like YC-backed <a href="{$base_url}/companies/hellosign">HelloSign</a> because of its unique Gmail/Google Apps integration functionality. Featuring prominently in the <a href="https://chrome.google.com/webstore/detail/hellosign-online-signatur/kajjckmbclbffbpecfbiecehkfgopppd?hl=en-US&utm_source=chrome-ntp-launcher">Chrome Apps store</a>, <a href="{$base_url}/companies/hellosign">HelloSign</a> tries to make their eSignature product a seamless integration within the Google Apps workflow. If you live off Google like we do, then this is an extremely valuable app, particularly in the <a href="http://blog.hellosign.com/hellosignchromeapp/">small business</a> or <a href="http://blog.hellosign.com/e-signature-realestate/">real estate</a> community.
                    </div>

                    <div class="vendor_story_block">
                        <div class="vendor_story_logo"><img class="" src="/images/splash/glass.png"></div>
                        <div class="vendor_story_title">
                            <span class="case_study_title">CASE STUDY:
                                <a class="vendor_story_link" href="{$base_url}/companies/hellosign" >HELLOSIGN</a>&nbsp;&nbsp;
                            </span>
                            <a href="{$base_url}/companies/hellosign">
                                <img class="vendor_logo_story" src="{$base_url}/logos/hellosign_v_thumb.jpg">
                            </a>
                        </div>
                        <div class="vendor_story_text">
                            <p>Signatures. Made simple. Getting documents signed has never been simpler with HelloSign. We’ve removed paper entirely from the process and built tools to facilitate document signing, tracking and management. Notifications keep you apprised of the signer’s activity and our audit trail logs it. Once completed, signed documents are securely stored and accessible from the cloud so you can always get to your important documents.
                        </div>
                        <div class="vendor_story_quote">
                            <p>&ldquo;Make signing the final contract as easy as possible for your client. Setup common sales documents as templates and fill in the client’s details for them them so all they have to do is e-Sign. This will lead to fewer errors, less back and forth, and faster closings.&rdquo;
                        </div>
                        <span class="vendor_story_author">&mdash;Neal O’Mara, CTO, <a class="vendor_story_link" href="{$base_url}/companies/hellosign" >HelloSign</a></span>
                    </div>

                    <div class="chapter_text">
                        <p><a href="{$base_url}/companies/docusign">DocuSign</a> is arguably the leading vendor in the space. Since launching in 2003, <a href="{$base_url}/companies/docusign">DocuSign</a> has processed more than 40 million documents from people and enterprises. <a href="{$base_url}/companies/docusign">DocuSign</a> is also the official and exclusive provider of electronic signature for the <a href="http://www.realtor.org/programs/realtor-benefits-program/technology-tools">National Association of REALTORS under the REALTOR Benefits Program</a>. With a million-plus members, NAR members include brokers, salespeople, property managers, appraisers, counselors and others engaged in all aspects of the real estate industry. <a href="{$base_url}/companies/docusign">DocuSign</a> also has a very popular app for mobile and tablet called <a href="http://www.docusign.com/products-and-pricing/mobile">DocuSign Ink</a>, available on the Apple App Store, Google Play and Windows Store. With <a href="http://www.businessinsider.com/keith-krach-docusign-future-2012-7">approximately 90% of the Fortune 500</a> on <a href="{$base_url}/companies/docusign">DocuSign</a>, they are very much the 800lb gorilla in the room. Customers of <a href="{$base_url}/companies/docusign">DocuSign</a> include <a href="{$base_url}/companies/box">Box</a>, <a href="{$base_url}/companies/linkedin">LinkedIn</a> and <a href="{$base_url}/companies/salesforcecom">Salesforce</a>.
                    </div>

                </div>

                {* CRM *}
                <div id="chapter_title_crm">
                    <div class="part_title">CRM</div>
                    <div class="part_intro_text">
                        <p>All this can’t be done without the mission control of sales, the customer relationship management (CRM) system. All modern CRM systems layer on the best applications (analytics, presentation and closing) into their platform. Here are the vendors you should consider as you’re tracking your sales through the funnel.
                    </div>

                    {* TRADITIONAL *}
                    <div class="chapter_title" id="chapter_title_{$sale_space_info.3.vendor_groups.0.scroll_id}">
                        TRADITIONAL
                    </div>
                    <div class="chapter_content">

                        <div class="chapter_vendors">
                            {foreach from=$sale_space_info.3.vendor_groups.0.vendors key=v_id item=v}
                                <div class="chapter_vendor_block fleft">
                                    <div class="chapter_vendor_logo">
                                        <a href="/companies/{$v.code_name}">
                                            <img data-codeName="{$v.code_name}" class="chapter_vendor_icon"  alt="{$v.vendor_name}" {if $v.local}src="/logos/{$v.logo_hash}_thumb.jpg"{else}src="{$base_url}/logos/{$v.logo_hash}_thumb.jpg"{/if}/>
                                        </a>
                                    </div>
                                    <div class="chapter_vendor_name">{$v.vendor_name|upper}</div>
                                </div>
                            {/foreach}
                            <div class="clear"></div>
                        </div>
                    </div>

                    {* DATA-DRIVEN/UPCOMING *}
                    <div class="chapter_title" id="chapter_title_{$sale_space_info.3.vendor_groups.1.scroll_id}">
                        DATA-DRIVEN/UPCOMING
                    </div>
                    <div class="chapter_content">

                        <div class="chapter_vendors">
                            {foreach from=$sale_space_info.3.vendor_groups.1.vendors key=v_id item=v}
                                <div class="chapter_vendor_block fleft">
                                    <div class="chapter_vendor_logo">
                                        <a href="/companies/{$v.code_name}">
                                            <img data-codeName="{$v.code_name}" class="chapter_vendor_icon"  alt="{$v.vendor_name}" {if $v.local}src="/logos/{$v.logo_hash}_thumb.jpg"{else}src="{$base_url}/logos/{$v.logo_hash}_thumb.jpg"{/if}/>
                                        </a>
                                    </div>
                                    <div class="chapter_vendor_name">{$v.vendor_name|upper}</div>
                                </div>
                            {/foreach}
                            <div class="clear"></div>
                        </div>
                    </div>

                    <div class="chapter_text">
                        <p>There is a lot of data about your customers online, from tweets to blogs to Facebook posts. All this information creates an interest graph, which you can use to help close the sale. Social selling works because that’s where we spend our time. Marketers know this and have targeted their content marketing appropriately.
                        <p>In a <a href="http://contentmarketinginstitute.com/wp-content/uploads/2013/04/Enterprise_Research_2013_CMI1.pdf">recent study</a> by the Content Marketing Institute and Marketo, they ranked the top distribution channels for marketers. Not surprisingly, Facebook and Twitter lead the pack. What is interesting is the top rankings for YouTube and other media-focused properties like SlideShare, Pinterest, Flikr and Instagram. The takeaway is that enterprise buyers consume content from a variety of different sources and next-generation CRMs that are able to aggregate and analyze these datapoints are going to win the day (and help you win the customer). Here are a few we like outside the traditional vendors.
                        <p><a href="{$base_url}/companies/handshakez">Handshakez</a> seeks to disrupt the traditional email-marketing CRM with <a href="http://handshakez.com/features/#private-collaborative-rooms">collaborative rooms</a>. Each room acts like a private channel for the buyer and seller. Because each room is scored on its temperature index, a “hot” room means that the customer is engaging frequently and in high quality with the sales rep. The rooms go beyond just chat. You can upload <a href="http://handshakez.com/features/#rich-media-collateral">rich media content</a> like presentations, videos and spreadsheets into the room and connect with outside channels like social media networks and yes, even email.  Customers of <a href="{$base_url}/companies/handshakez">Handshakez</a> include <a href="{$base_url}/companies/jama_software">Jama</a>, <a href="{$base_url}/companies/alterpoint">AlterPoint</a> and <a href="{$base_url}/companies/winzip_computing">WinZip Computing</a>.
                    </div>

                    <div class="vendor_story_block">
                        <div class="vendor_story_logo"><img class="" src="/images/splash/glass.png"></div>
                        <div class="vendor_story_title">
                            <span class="case_study_title">CASE STUDY:
                                <a class="vendor_story_link" href="{$base_url}/companies/handshakez" >HANDSHAKEZ</a>&nbsp;&nbsp;
                            </span>
                            <a href="{$base_url}/companies/handshakez">
                                <img class="vendor_logo_story" src="{$base_url}/logos/handshakez_v_thumb.jpg">
                            </a>
                        </div>
                        <div class="vendor_story_text">
                            <p>Handshakez serves as the bridge between the lead and the forecast phases of a sales process.  We help sales teams engage with customers when it matters most - in the opportunity.
                        </div>
                        <div class="vendor_story_quote">
                            <p>&ldquo;Engagement is the new currency of B2B.  But customers are largely bored with your sales deck and product slick.  And they are only responding to 25% of the email you send them.  If the goal is to engage them, then you need to present them differentiated content in a differentiated fashion.  We analyze thousands of B2B sales interactions every day and are creating the special sauce for how to inspire & motivate your customers.&rdquo;
                        </div>
                        <span class="vendor_story_author">&mdash;Jason Wesbecher, CEO, <a class="vendor_story_link" href="{$base_url}/companies/handshakez" >Handshakez</a></span>
                    </div>

                    <div class="vendor_story_block">
                        <div class="vendor_story_logo"><img class="" src="/images/splash/glass.png"></div>
                        <div class="vendor_story_title">
                            <span class="case_study_title">CASE STUDY:
                                <a class="vendor_story_link" href="{$base_url}/companies/relateiq" >RELATEIQ</a>&nbsp;&nbsp;
                            </span>
                            <a href="{$base_url}/companies/relateiq">
                                <img class="vendor_logo_story" src="{$base_url}/logos/relateiq_v_thumb.jpg">
                            </a>
                        </div>
                        <div class="vendor_story_text">
                            <p>Helping teams track, share, and analyze their most important professional relationships, RelateIQ has rethought the problem of relationship management from the ground up.
                        </div>
                        <div class="vendor_story_quote">
                            <p>&ldquo;Sales teams amass huge quantities of data—more than can be tracked manually in a traditional CRM. RelateIQ automatically aggregates data from email, calendars, social networks, and phone calls, entering data for you while you and your team work. Not only does this eliminate manual data entry, but also allows you to mine your own data for key insights, such as who needs your attention right now and where you are in danger of dropping the ball next week.&rdquo;
                        </div>
                        <span class="vendor_story_author">&mdash;Steve Loughlin, Marketing, <a class="vendor_story_link" href="{$base_url}/companies/relateiq" >RelateIQ</a></span>
                    </div>

                    <div class="chapter_text">
                        <p><a href="{$base_url}/companies/nimble">Nimble</a> tackles the challenge of dealing with multiple online profiles, social networks and distribution channels by integrating them smartly in a simple unified interface. <a href="http://www.nimble.com/social-listening/">Users can not only check updates</a> from Facebook, Twitter, Gmail and LinkedIn in the same feed, you can also respond to messages or book meetings in a unified inbox and calendar. <a href="{$base_url}/companies/nimble">Nimble</a> can also act as social unified communication layer on top of popular apps including traditional CRM tools like <a href="{$base_url}/companies/salesforcecom">Salesforce</a> and <a href="{$base_url}/companies/sugarcrm">SugarCRM</a>, email marketing vendors like <a href="{$base_url}/companies/mailchimp">MailChimp</a> and <a href="{$base_url}/companies/aweber">AWeber</a> and eCommerce platforms like <a href="{$base_url}/companies/magento">Magento</a> and <a href="{$base_url}/companies/shopify">Shopify</a>. Customers of <a href="{$base_url}/companies/nimble">Nimble</a> include <a href="{$base_url}/companies/grasshopper">Grasshopper</a>, <a href="{$base_url}/companies/skymax">Skymax</a> and <a href="{$base_url}/companies/intermedio_information_technology">Intermedio</a>.
                    </div>

                    <div class="vendor_story_block">
                        <div class="vendor_story_logo"><img class="" src="/images/splash/glass.png"></div>
                        <div class="vendor_story_title">
                            <span class="case_study_title">CASE STUDY:
                                <a class="vendor_story_link" href="{$base_url}/companies/nimble" >NIMBLE</a>&nbsp;&nbsp;
                            </span>
                            <a href="{$base_url}/companies/nimble">
                                <img class="vendor_logo_story" src="{$base_url}/logos/nimble_v_thumb.jpg">
                            </a>
                        </div>
                        <div class="vendor_story_text">
                            <p>Nimble is the only solution to offer small businesses the best features of high-end CRM systems combined with the communication power of social media and collaborative tools… all in one simple and affordable SaaS solution.
                        </div>
                        <div class="vendor_story_quote">
                            <p>&ldquo;Relationships make or break your business— and they’re hard to manage in our noisy world. We built Nimble to smartly combine all email, social signals, activities, and follow-ups in one place. View up-to-the-minute information about a contact and be ready to engage in real-time.&rdquo;
                        </div>
                        <span class="vendor_story_author">&mdash;Rachel Miller, Customer Engagement Manager, <a class="vendor_story_link" href="{$base_url}/companies/nimble" >Nimble</a></span>
                    </div>

                </div>


                {* MENTIONED *}
                <div id="chapter-mentioned">
                    <div class="part_title">
                        VENDORS MENTIONED<span class="simple_text_in_title">Sorted by taxonomy, then alphabetically</span>
                    </div>
                    <div class="chapter_content">
                        <table class="text_table">
                            <tr>
                                <td>
                                    <span class="table_subheader">{$sale_space_info.0.vendor_groups.0.title}</span><br><br>
                                    {foreach from=$sale_space_info.0.vendor_groups.0.vendors key=v_id item=v}
                                        <a href="{$base_url}/companies/{$v.code_name}">{$v.vendor_name|capitalize}</a><br>
                                    {/foreach}
                                    <br>
                                    <span class="table_subheader">{$sale_space_info.0.vendor_groups.1.title}</span><br><br>
                                    {foreach from=$sale_space_info.0.vendor_groups.1.vendors key=v_id item=v}
                                        <a href="{$base_url}/companies/{$v.code_name}">{$v.vendor_name|capitalize}</a><br>
                                    {/foreach}
                                </td>
                                <td>
                                    <span class="table_subheader">{$sale_space_info.1.vendor_groups.0.title}</span><br><br>
                                    {foreach from=$sale_space_info.1.vendor_groups.0.vendors key=v_id item=v}
                                        <a href="{$base_url}/companies/{$v.code_name}">{$v.vendor_name|capitalize}</a><br>
                                    {/foreach}
                                    <br>
                                    <span class="table_subheader">{$sale_space_info.1.vendor_groups.1.title}</span><br><br>
                                    {foreach from=$sale_space_info.1.vendor_groups.1.vendors key=v_id item=v}
                                        <a href="{$base_url}/companies/{$v.code_name}">{$v.vendor_name|capitalize}</a><br>
                                    {/foreach}
                                    <br>
                                    <span class="table_subheader">{$sale_space_info.2.vendor_groups.0.title}</span><br><br>
                                    {foreach from=$sale_space_info.2.vendor_groups.0.vendors key=v_id item=v}
                                        <a href="{$base_url}/companies/{$v.code_name}">{$v.vendor_name|capitalize}</a><br>
                                    {/foreach}
                                </td>
                                <td>
                                    <span class="table_subheader">{$sale_space_info.3.vendor_groups.0.title}</span><br><br>
                                    {foreach from=$sale_space_info.3.vendor_groups.0.vendors key=v_id item=v}
                                        <a href="{$base_url}/companies/{$v.code_name}">{$v.vendor_name|capitalize}</a><br>
                                    {/foreach}
                                    <br>
                                    <span class="table_subheader">{$sale_space_info.3.vendor_groups.0.title}</span><br><br>
                                    {foreach from=$sale_space_info.3.vendor_groups.1.vendors key=v_id item=v}
                                        <a href="{$base_url}/companies/{$v.code_name}">{$v.vendor_name|capitalize}</a><br>
                                    {/foreach}
                                    <br>
                                    We’d like to thank your graphic designer, <a href="http://jessicasuen.com/">Jessica Suen</a> for designing this infographic.
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                {* DISCLAIMERS *}
                <div id="disclaimers">
                    <div class="disclaimer_title">
                        DISCLAIMERS
                    </div>

                    <div class="part_intro_text">
                        <p><span class="disclaime_sublitle">Geographic Bias</span>. Many of the vendors and pain points mentioned in this infographic are US-centric, particularly California-centric.  We follow the old adage of think globally but act locally and we hope we can service our customers far and abroad as well as we do our SF Bay Area colleagues.
                        <p><span class="disclaime_sublitle">Vendor impartiality</span>. We asked some vendors to articulate where they provide the biggest value to their customers.  Our request was that each quote was given with the end user in mind and to try not to be too commercial.  We realize that by highlighting certain vendors over others, we are giving some preferential treatment even when that is not our intent.  If you’re a vendor who would like to be showcased in future reports, contact us at vendors@sharebloc.com and we’ll get back to you shortly.  Thanks!
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div id="popups">
        {foreach from=$sale_space_info key=column_id item=column}
            {foreach from=$column.vendor_groups key=group_id item=group}
                {foreach from=$group.vendors key=vendor_id item=v}
                    <div data-codeName="{$v.code_name}" data-gID="{$group_id}" id="popup_div_{$v.code_name}" class="popup_div">
                        <div class="standard_popup">
                            <div class="vendor_icon_div">
                                <a href="/companies/{$v.code_name}"><img class="popup_vendor_icon" alt="{$v.vendor_name}" src="/logos/{$v.logo_hash}_thumb.jpg"/></a>
                            </div>
                            <div class="popup_vendor_info">
                                <div class="popup_vendor_title"><a class="vendor_title_link" href="/companies/{$v.code_name}">{$v.vendor_name}</a></div>
                                <div class="vendor_tags">
                                    <a class="vendor_tag_link" href="/blocs/{$v.category.parent_tag_code_name}">{$v.category.parent_tag_name}</a>
                                    {if !empty($v.category.code_name)}
                                        <span class="divider"> &middot; </span>
                                        <a class="vendor_tag_link" href="/blocs/{$v.category.code_name}">{$v.category.tag_name}</a>
                                    {/if}
                                </div>
                            </div>
                            <div class="clear"></div>
                            <div class="vendor_overview">
                                <span class="overview_text">{$v.description}</span>
                            </div>
                        </div>
                    </div>
                {/foreach}
            {/foreach}
        {/foreach}
    </div>
    <div class="size_indicator"></div>
    <div class="footer_block">
        <div class="block_content">
            <div class="footer_block_content">
                <a class="footer_link" href="/team">Team</a>
                <a class="footer_link" href="/terms">Terms</a>
                <a class="footer_link" href="/privacy">Privacy</a>
                <a class="footer_link" href="http://blog.vendorstack.com/">Blog</a>
                <a class="footer_link" href="/vendorcontact.php">Are you a Vendor?</a>
                <a class="footer_link" href="/resources.php">Resources</a>
            </div>
        </div>
    </div>

    {* js is placed at the bottom to not slow down other resources loading *}
    <script type="text/javascript" src="/js/jquery-1.7.1.min.js"></script>
    {if $dev_mode && $shouldUseCssRefresh}
    <script src="/js/css_refresh.js" type="text/javascript"></script>
    {/if}
    <script type="text/javascript">
        var is_on_top = true;
        var curr_popup = false;
        var show_arr = { };
        var mouseX, mouseY;
        $(document).ready(function() {
            $(window).scroll(function() {
                updateDownloadBlock();
            });

            $('.scroll_link').click(function() {
                scrollToChapter($(this).attr('data-chid'));
                return false;
            });
            $("#username_link").mouseover(function() {
                $("#user_menu").removeClass('hidden');
                $("#username_link").addClass('header_person_hover');
            });

            $("#user_menu_content").mouseleave(function() {
                $("#user_menu").addClass('hidden');
                $("#username_link").removeClass('header_person_hover');
            });

        {if $is_ie8}
            $(".ie8_alert").fadeOut(10000);
        {else}
            $(document).mousemove(function(e) {
                mouseX = e.pageX;
                mouseY = e.pageY;
            });

            $('.bubble_link').click(function() {
                scrollToQuestion($(this).attr('data-gid'));
                return false;
            });

            $('.vendor_icon').click(function() {
                return true;
            });

            $('.vendor_icon, .chapter_vendor_icon, .popup_div').hover(
                    function(e) {
                        var temp_el = $(this);
                        var code_name = temp_el.attr("data-codeName");

                        if (show_arr[code_name] === undefined) {
                            show_arr[code_name] = 0;
                        }
                        show_arr[code_name]++;
                        showTimeoutID = setTimeout(
                                function() {
                                    showPopup(temp_el);
                                }, 400);
                        return false;
                    },
                    function(e) {
                        var code_name = $(this).attr("data-codeName");
                        show_arr[code_name]--;
                        if (curr_popup) {
                            hideTimeoutID = setTimeout(
                                    function() {
                                        hidePopup(code_name);
                                    }, 100);
                        } else {
                            hidePopup(code_name);
                        }
                        return false;
                    }
            );
        {/if}
            updateDownloadBlock();
        });


        function updateDownloadBlock() {
            var currentOffset = $(window).scrollTop();
            if (currentOffset > 0 && is_on_top) {
                is_on_top = false;
                $(".fixed").removeClass("fixed").addClass("scroll");
            } else if (currentOffset === 0 && !is_on_top) {
                is_on_top = true;
                $(".scroll").removeClass("scroll").addClass("fixed");
            }
        }

        function scrollToChapter(ch_id) {
            var shift = $(window).height() / 3;
            var target = $("#chapter_title_" + ch_id);
            var scroll_to = target.offset().top - shift;
            $("html,body").animate({
                "scrollTop": scroll_to
            }, "slow");
            return false;
        }


        function showPopup(el) {
            var x = mouseX;
            var y = mouseY;
            var code_name = el.attr("data-codeName");

            //var q_num = el.attr("data-gID");
            //var is_left = q_num == 1 || q_num == 4 || q_num == 6;

            if (show_arr[code_name] < 1) {
                return false;
            }

            if (code_name == curr_popup) {
                return false;
            }

            var popup_div = $("#popup_div_" + code_name);

            var popup_width = popup_div.width();
            var win_width = $(window).width();
            var right_distance = win_width - (mouseX + popup_width);
            var left_distance = mouseX - popup_width;

            var is_left = left_distance < right_distance;

            y = y + 10;
            if (!is_left) {
                x = x - popup_width;
                x = x - 15;
            } else {
                x = x + 15;
            }

            if (x < 20) {
                x = 20;
            }

            popup_div.css('top', y + 'px');
            popup_div.css('left', x + 'px');

            $(".popup_div").hide();
            popup_div.show();
            curr_popup = code_name;
            should_show = null;
            return false;
        }

        function hidePopup(code_name) {
            if (show_arr[code_name] > 0) {
                return false;
            }
            $("#popup_div_" + code_name).hide();
            curr_popup = false;
            return false;
        }

        var _gaq = _gaq || [];
        _gaq.push(['_setAccount', 'UA-29473234-1']);
        _gaq.push(['_setDomainName', 'vendorstack.com']);
        _gaq.push(['_trackPageview']);

        (function() {
            var ga = document.createElement('script');
            ga.type = 'text/javascript';
            ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0];
            s.parentNode.insertBefore(ga, s);
        })();
    </script>
</body>
</html>
