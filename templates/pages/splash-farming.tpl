<!DOCTYPE html>
<html>
    <head>
        <title>ShareBloc - Lead Farming: Three steps to grow leads.</title>
        <meta name="keywords" content="business content, content discovery, enterprise, b2b, SMB, small medium business, lead-gen"/>
        <meta name="description" content="ShareBloc is a community of like-minded professionals who share, curate and discuss business content that matters.">
        <meta http-equiv="content-type" content="text/html; charset=utf-8">
        <link rel="shortcut icon" href="/favicon.png" type="image/x-icon">
        <link rel="stylesheet" href="css/infographics/splash-farming.css" type="text/css" />
    </head>
    <body>
        <div class="download_block">
            <div class="download_link_div fixed">
                <div class="download_block_content">
                    <a href="{$index_page}"><img class="vslogo_header" alt="ShareBloc" src="/images/sharebloc_logo.png"></a>
                <a class="download_link fixed" href="/files/Lead_Farming_Three_Steps_to_Grow_Leads.pdf">Download the PDF</a>

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
                {* left column *}
                <div class="color_block">
                    <div class="upper_block">
                        <div class="infographic_title shadow scroll_link" data-chid="textstart">
                            <div class="lead_header">LEAD</div>
                            <div class="farming_header"><span class="strike">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> FARMING <span class="strike">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></div>
                            <div class="infographic_border">
                            </div>
                        </div>
                        <div class="upper_block_title shadow scroll_link" data-chid="plant">PLANT</div>
                        <div class="upper_block_text">First, identify and contextualize the lead. <br>Get more than just a name and number. <br>Make sure you get information to <br>understand where they <br>are coming from.</div>
                        <div class="upper_block_image spade"><img src="/images/resources/spade.png"></div>
                        <!--div class="upper_block_waves"></div-->
                    </div>
                    <div class="upper_block_waves"></div>
                    <div class="lower_block">
                        {foreach from=$lead_farming_info.0 key=col_id item=vendor_group}
                            <div class="lower_block_vendors">
                                <div class="vendor_group_title scroll_link" data-chid="{$vendor_group.scroll_id}">{$vendor_group.text_title}</div>
                                {foreach from=$vendor_group.vendors key=id item=v}
                                    <div class="fleft">
                                        <a href="/companies/{$v.code_name}">
                                            <img data-codeName="{$v.code_name}" class="vendor_icon"  alt="{$v.vendor_name}" {if $v.local}src="/logos/{$v.logo_hash}_thumb.jpg"{else}src="{$base_url}/logos/{$v.logo_hash}_thumb.jpg"{/if}/>
                                        </a>
                                    </div>
                                {/foreach}
                            </div>
                        {/foreach}
                        <br class="clear">
                        <div class="lower_block_protip">
                            <div class="lower_block_protip_title">PRO-TIP</div>
                            <div class="lower_block_protip_text">"Using a social network to listen and identify new prospects lets me leverage my connections to get warm introductions. Focus on adding value to your prospects and less on selling to them."</div>
                            <div class="lower_block_protip_author">
                                <span class="lower_block_protip_author_span">&mdash; KOKA SEXTON</span> | SR. SOCIAL MARKETING MANAGER, <a href="{$base_url}/companies/linkedin" class="lower_block_protip_author_link">LINKEDIN</a>
                            </div>
                        </div>
                        <div class="lower_block_protip">
                            <div class="lower_block_protip_title green_title">PRO-TIP</div>
                            <div class="lower_block_protip_text">"Tools when used incorrectly with the wrong strategy and targets can do more harm than good. But if you do a bit of research and collect business intelligence and discover social connections, it makes a tremendous difference in the customer experience and response rate."</div>
                            <div class="lower_block_protip_author">
                                <span class="lower_block_protip_author_span">&mdash; ANNEKE SELEY</span> | EVANGELIST &amp; FOUNDER, <a href="{$base_url}/companies/reality_works_group" class="lower_block_protip_author_link">REALITY WORKS GROUP</a>
                            </div>
                        </div>
                    </div>
                    <div class="bottom_block_waves"></div>
                </div>
                {* center column *}
                <div class="color_block">
                    <div class="upper_block">
                        <div class="upper_block_wo_title">
                            <div class="upper_title">Three steps to grow leads</div>
                            <div class="upper_text">Lead generation is a process. Like a crop, you can’t get just acquire a raw lead and bring it to market. Leads have to be nurtured over time before they can be properly harvested. </div>
                        </div>
                        <div class="upper_block_title shadow scroll_link" data-chid="nurture">NURTURE</div>
                        <div class="upper_block_text">Now that you have your lead, <br>you have to nurture them with <br>various touch points to better <br>qualify them. Make sure your <br>leads see you as a source of <br>industry leading information. <br>Use one of these methods.</div>
                        <div class="upper_block_image water"><img src="/images/resources/watering_the_plant.png"></div>
                        <!--div class="upper_block_waves"></div-->
                    </div>
                    <div class="upper_block_waves"></div>
                    <div class="lower_block lower_block_longest">
                        {foreach from=$lead_farming_info.1 key=col_id item=vendor_group}
                            <div class="lower_block_vendors">
                                <div class="vendor_group_title scroll_link" data-chid="{$vendor_group.scroll_id}">{$vendor_group.text_title}</div>
                                {foreach from=$vendor_group.vendors key=id item=v}
                                    <div class="fleft">
                                        <a href="/companies/{$v.code_name}">
                                            <img data-codeName="{$v.code_name}" class="vendor_icon"  alt="{$v.vendor_name}" {if $v.local}src="/logos/{$v.logo_hash}_thumb.jpg"{else}src="{$base_url}/logos/{$v.logo_hash}_thumb.jpg"{/if}/>
                                        </a>
                                    </div>
                                {/foreach}
                            </div>
                        {/foreach}
                        <br class="clear">
                        <div class="lower_block_protip smaller_protip_top_padding">
                            <div class="lower_block_protip_title">PRO-TIP</div>
                            <div class="lower_block_protip_text">"TEST TEST TEST! Way too often email marketers get caught in the rut of sending the emails formatted the exact same way, only changing the content as needed. However, not all emails are the same and not all emails have the same goal."</div>
                            <div class="lower_block_protip_author">
                                <span class="lower_block_protip_author_span">&mdash; ADAM TUTTLE</span> | PARTNER &amp; BUSINESS DEVELOPMENT, <a href="{$base_url}/companies/activecampaign" class="lower_block_protip_author_link">ACTIVECAMPAIGN</a>
                            </div>
                        </div>
                        <div class="lower_block_protip smaller_protip_top_padding">
                            <div class="lower_block_protip_title green_title">PRO-TIP</div>
                            <div class="lower_block_protip_text">"What's changing is the use of data and analytics in the marketing stack.  Decisions are being made based on measurable ROI, including via social.  Engagement with potential customers and users is critical; and leveraging ambassadors for referrals has never been more impactful than it is today."</div>
                            <div class="lower_block_protip_author">
                                <span class="lower_block_protip_author_span">&mdash; JEFF EPSTEIN</span> | CHIEF AMBASSADOR, <a href="{$base_url}/companies/ambassador" class="lower_block_protip_author_link">AMBASSADOR</a>
                            </div>
                        </div>
                    </div>
                    <div class="bottom_block_waves"></div>
                </div>
                {* right column *}
                <div class="color_block">
                    <div class="upper_block">
                        <div class="upper_block_wo_title">
                            <div class="upper_title"></div>
                            <div class="upper_text">We identified 40 enterprise vendors that help marketers better farm their leads. There are three main stages of lead farming, each broken down by sub-categories. </div>
                        </div>
                        <div class="upper_block_title shadow scroll_link" data-chid="harvest">HARVEST</div>
                        <div class="upper_block_text">Now that your leads are engaged, <br>it’s time to take them through the <br>end of the marketing funnel towards <br>your sales team. These vendors <br>all help bubble up leads that <br>are ready to be harvested.</div>
                        <div class="upper_block_image basket"><img src="/images/resources/harvesting_basket.png"></div>
                        <!--div class="upper_block_waves"></div-->
                    </div>
                    <div class="upper_block_waves"></div>
                    <div class="lower_block">
                        {foreach from=$lead_farming_info.2 key=col_id item=vendor_group}
                            <div class="lower_block_vendors">
                                <div class="vendor_group_title scroll_link" data-chid="{$vendor_group.scroll_id}">{$vendor_group.text_title}</div>
                                {foreach from=$vendor_group.vendors key=id item=v}
                                    <div class="fleft">
                                        <a href="/companies/{$v.code_name}">
                                            <img data-codeName="{$v.code_name}" class="vendor_icon"  alt="{$v.vendor_name}" {if $v.local}src="/logos/{$v.logo_hash}_thumb.jpg"{else}src="{$base_url}/logos/{$v.logo_hash}_thumb.jpg"{/if}/>
                                        </a>
                                    </div>
                                {/foreach}
                            </div>
                        {/foreach}
                        <br class="clear">
                        <div class="lower_block_protip">
                            <div class="lower_block_protip_title">PRO-TIP</div>
                            <div class="lower_block_protip_text">"For marketing and sales to be aligned, they need to measure themselves on a ‘common currency’ of an objectively qualified lead.  Once both agree on a definition of what behavior determines qualification, marketing can commit to sourcing specific numbers, and sales can commit to follow up speeds and close rates."</div>
                            <div class="lower_block_protip_author">
                                <span class="lower_block_protip_author_span">&mdash; STEVE WOODS</span> | CTO &amp; CO-FOUNDER, <a href="{$base_url}/companies/eloqua" class="lower_block_protip_author_link">ELOQUA</a>
                            </div>
                        </div>
                        <div class="lower_block_protip">
                            <div class="lower_block_protip_title green_title">PRO-TIP</div>
                            <div class="lower_block_protip_text">"Integrating lead nurturing with your customer database allows you to value each potential customer and gives indications of where they are in their purchasing path. The biggest pitfall to marketing automation purchases is not dedicating the resources to properly integrate the package."</div>
                            <div class="lower_block_protip_author">
                                <span class="lower_block_protip_author_span">&mdash; KATYA CONSTANTINE</span> | FOUNDER, <a href="{$base_url}/companies/digishopgirl_media" class="lower_block_protip_author_link">DIGISHOPGIRL MEDIA</a>
                            </div>
                        </div>
                    </div>
                    <div class="bottom_block_waves">
                        <div class="vslogo_on_the_waves_div"><a href="/"><img class="vslogo_on_the_waves" alt="ShareBloc" src="/images/vendorstack_logo_grey.png"></a></div>
                    </div>
                </div>
                <br class="clear">
            </div>
        </div>
        <div class="text_block_container">
            <div class="infographic_title_content">
                <div class="block_title">
                    <div class="huge_lead_title">LEAD</div>
                    <div class="huge_farming_title"><span class="strike">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> FARMING <span class="strike">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></div>
                    <div class="non-semantic-protector">
                        <p class="ribbon">
                            <span class="ribbon-content"><span class="ribbon-text">Three steps to grow leads</span></span>
                        </p>
                    </div>
                </div>
            </div>
            <br>
            <div class="infographic_block_content" id="chapter_title_textstart">
                {* ADDENDUM *}
                <div>
                    <div class="part_title">ADDENDUM</div>
                    <div class="part_intro_text">
                        <p>Lead generation is a process. Like a crop, you can’t get just acquire a raw lead and bring it to market. Leads have to be nurtured over time before they can be properly harvested.
                        <p>We identified 40 enterprise vendors that help marketers better farm their leads. There are three main stages of lead farming, each broken down by sub-categories.
                    </div>

                    <div class="chapter_title">
                        WHAT IS A QUALIFIED MARKETING LEAD?
                    </div>

                    <div class="chapter_content">
                        <div class="chapter_text">
                            <p>Leads as described by <a href="http://www.eloqua.com/resources/best-practices/qualified-leads.html">Eloqua are individuals who are displaying both the intent and the capacity to make a buying decision in a reasonable timeframe</a>.  To qualify a lead, <a href="{$base_url}/companies/eloqua">Eloqua</a> adds that it’s typically based on explicit information: their job title, industry, company revenue, and geography. <a href="http://www.idc.com/eagroup/download/accelerating-new-buyers-journey.pdf">However, buyers spend 80-90% outside of their buying cycle.</a> The best leads are thus typically found at the very beginning of the buying cycle, when the enterprise consumer is looking to make a purchase.
                            <p>An enterprise lead can be as low as $20 to $30 for a cost per click (although it is arguable that a click is not a lead) to <a href="http://trueinfluence.com/marketing-industry-research-reports/b2b-cpl-benchmark-survey-2011/">upwards to $100+ for a highly targeted, highly qualified lead</a>.
                            <p>We identified three main stages of lead farming, each broken down by sub-categories, to best plant, nurture and harvest a lead.
                        </div>
                    </div>
                </div>

                {* PLANTING A LEAD *}
                <div id="chapter_title_plant">
                    <div class="part_title">
                        <div class="part_title_image"><img src="/images/resources/seeds.png"></div>
                        PLANTING A LEAD
                    </div>
                    <div class="part_intro_text">
                        <p>First, identify and contextualize the lead. Get more than just a name and number. Make sure you get information to understand where they are coming from.
                    </div>

                    {* IDENTIFY LEADS *}
                    <div class="chapter_title" id="chapter_title_identify">
                        IDENTIFY LEADS
                    </div>
                    <div class="chapter_content">

                        <div class="chapter_vendors">
                            {foreach from=$lead_farming_info.0.0.vendors key=v_id item=v}
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

                        <div class="chapter_text">
                            <p>The first step in the lead generation process is to acquire your leads. We have identified seven vendors who can help you fill out your lead funnel. These vendors provide more than just basic contact information.
                            <p>Some vendors like <a href="{$base_url}/companies/linkedin">LinkedIn</a>, <a href="{$base_url}/companies/hoovers">Hoover’s</a>, <a href="{$base_url}/companies/datacom">Data.com</a> and <a href="{$base_url}/companies/netprospex">NetProspex</a> already have databases of contact information for key decisions makers from the C-suite down. Many also leverage social media content and second-degree relationships to provide more data to marketers.
                        </div>

                        <div class="chapter_protip">
                            <div class="chapter_protip_quotes">	&ldquo;</div>
                            <div class="chapter_protip_title">PRO-TIP</div>
                            <div class="chapter_protip_text">
                                <p>Using a social network to listen and identify new prospects lets me leverage my connections to get warm introductions. If you focus on how you are connected to prospects you will be much more likely to get the initial meeting. Focus on adding value to your prospects and less on selling to them. The more value you add the more your prospect is going to be receptive to you.&rdquo;
                                <p><span class="chapter_protip_author">&mdash; KOKA SEXTON</span> | SR. SOCIAL MARKETING MANAGER, <a href="{$base_url}/companies/linkedin" class="chapter_protip_link">LINKEDIN</a>
                            </div>
                        </div>

                        <div class="chapter_text">
                            <p>We’re excited about new vendors like <a href="{$base_url}/companies/leandata">LeanData</a>, <a href="{$base_url}/companies/mixrank">MixRank</a> and <a href="{$base_url}/companies/toofr">Toofr</a> who provide additional data on your leads, usually by pulling data and content from non-traditional data sources.
                        </div>

                        {* Leandata story *}
                        <div class="vendor_story_block">
                            <div class="vendor_story_logo"><img class="vendor_logo_story" src="{$base_url}/logos/leandata_v_thumb.jpg"></div>
                            <div class="vendor_story_title">CASE STUDY: <a class="vendor_story_link" href="{$base_url}/companies/leandata" >LEANDATA</a></div>
                            <div class="vendor_story_text">
                                <p>LeanData provides a turnkey, fully managed service to ensure the integrity of your vital business data. Using a SaaS-based workflow engine and the power of cloud labor to handle exception-driven fuzzy matching, LeanData can be up and running quickly, at a much lower cost than traditional software systems, making sure your enterprise data stays clean and accurate across all data inputs.
                            </div>
                            <div class="vendor_story_quote">
                                <p>&ldquo;LeanData’s combination of a native salesforce app, algorithms plus on-demand human intelligence has shown to decrease the cost of managing list uploads by 75% and increase the turnaround time by 5X.&rdquo;
                            </div>
                            <span class="vendor_story_author">&mdash; EVAN LIANG | CEO &amp; CO-FOUNDER, <a class="vendor_story_link" href="{$base_url}/companies/leandata" >LEANDATA</a></span>
                        </div>

                        {* Mixrank story *}
                        <div class="vendor_story_block">
                            <div class="vendor_story_logo"><img class="vendor_logo_story" src="{$base_url}/logos/mixrank_v_thumb.jpg"></div>
                            <div class="vendor_story_title">CASE STUDY: <a class="vendor_story_link" href="{$base_url}/companies/mixrank" >MIXRANK</a></div>
                            <div class="vendor_story_text">
                                <p>MixRank builds advertising analytics software that can automatically identify the highest performing ads for any advertiser or traffic source, enabling any performance advertiser to build successful display campaigns.
                            </div>
                            <div class="vendor_story_quote">
                                <p>&ldquo;Engaging with leads is no longer as simple as opening up the phone book and cold-calling your top prospects. Sales and marketing teams are now investing in data-driven prospecting to generate pre-qualified leads and higher response rates.&rdquo;
                            </div>
                            <span class="vendor_story_author">&mdash; JANA FUNG | MARKETING MANAGER, <a class="vendor_story_link" href="{$base_url}/companies/mixrank" >MIXRANK</a></span>
                        </div>

                        {* Toofr story *}
                        <div class="vendor_story_block">
                            <div class="vendor_story_logo"><img class="vendor_logo_story" src="{$base_url}/logos/toofr_v_thumb.jpg"></div>
                            <div class="vendor_story_title">CASE STUDY: <a class="vendor_story_link" href="{$base_url}/companies/toofr" >TOOFR</a></div>
                            <div class="vendor_story_text">
                                <p>Toofr can help you easily build email addresses for leads at 500,000 top companies. Save thousands of dollars on email lists - one pattern yields limitless contacts.
                            </div>
                            <div class="vendor_story_quote">
                                <p>&ldquo;Lower your email bounce rates by getting first and last names directly from the company website, LinkedIn, or news articles and deriving the email addresses from a proven pattern provided by Toofr or another service.&rdquo;
                            </div>
                            <span class="vendor_story_author">&mdash; RYAN BUCKLEY | CEO &amp; CO-FOUNDER, <a class="vendor_story_link" href="{$base_url}/companies/toofr" >TOOFR</a></span>
                        </div>

                    </div>


                    {* CONTEXTUALIZE LEADS *}
                    <div class="chapter_title" id="chapter_title_context">
                        CONTEXTUALIZE LEADS
                    </div>
                    <div class="chapter_content">
                        <div class="chapter_vendors">
                            {foreach from=$lead_farming_info.0.1.vendors key=v_id item=v}
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

                        <div class="chapter_text">
                            <p>Just getting their contact information, while comprehensive, doesn’t tell you about the enterprise consumer’s propensity to buy. New data points from social media streams, from likes or tweets can tell you a lot about your prospective lead. By developing a tangible process in prospecting your leads through different data channels, your marketing team can be effective in moving your lead down the marketing funnel.
                        </div>

                        {* Radius story *}
                        <div class="vendor_story_block">
                            <div class="vendor_story_logo"><img class="vendor_logo_story" src="{$base_url}/logos/radius_v_thumb.jpg"></div>
                            <div class="vendor_story_title">CASE STUDY: <a class="vendor_story_link" href="{$base_url}/companies/radius" >RADIUS</a></div>
                            <div class="vendor_story_text">
                                <p>For sales teams looking to sell to small and medium-sized businesses, Radius makes sense of constantly changing local business data to help you prospect smarter and close faster. Our real-time data collection and normalization processes enable us to create the most comprehensive repository of business data in the U.S.
                            </div>
                            <div class="vendor_story_quote">
                                <p>&ldquo;A successful salesperson today cultivates his or her digital presence. There are a number of critical components to your digital profile, including the information on your LinkedIn account, the people you follow and engage through Twitter, and the content you share to become a trusted resource. Build your personal brand on social media to attract the prospects that you want to discover you.&rdquo;
                            </div>
                            <span class="vendor_story_author">&mdash; DARREN WADDELL | MARKETING &amp; PRODUCT EXECUTIVE, <a class="vendor_story_link" href="{$base_url}/companies/radius" >RADIUS</a></span>
                        </div>

                        <div class="chapter_text">
                            <p>Ultimately, it doesn’t matter if you collect all the data on your lead without processing and synthesizing a coherent strategy.
                        </div>

                        <div class="chapter_protip">
                            <div class="chapter_protip_quotes">&ldquo;</div>
                            <div class="chapter_protip_title">PRO-TIP</div>
                            <div class="chapter_protip_text">
                                <p>Tools when used incorrectly with the wrong strategy and targets can do more harm than good. “Smile and dial” with a personal, relevant and timely reason to connect should be dead but isn't. But if you do a bit of research and collect business intelligence and discover social connections, it makes a tremendous difference in the customer experience and response rate. Be prepared before you email, call or connect socially and focus on what's in it for them.&rdquo;
                                <p><span class="chapter_protip_author">&mdash; ANNEKE SELEY</span> | EVANGELIST &amp FOUNDER, <a href="{$base_url}/companies/reality_works_group" class="chapter_protip_link">REALITY WORKS GROUP</a>
                            </div>
                        </div>

                    </div>
                </div>

                {* NURTURING A LEAD *}
                <div id="chapter_title_nurture">
                    <div class="part_title">
                        <div class="part_title_image"><img src="/images/resources/watering.png"></div>
                        NURTURING A LEAD
                    </div>
                    <div class="part_intro_text">
                        <p>Now that you have your lead, you have to nurture them with various touch points to better qualify them. Make sure your leads see you as a source of industry leading information. Use one of these methods.
                    </div>

                    {* E-MAIL MARKETING *}
                    <div class="chapter_title" id="chapter_title_email">
                        E-MAIL MARKETING
                    </div>

                    <div class="chapter_content">

                        <div class="chapter_vendors">
                            {foreach from=$lead_farming_info.1.0.vendors key=v_id item=v}
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

                        <div class="chapter_text">
                            <p>Email marketing remains a critical tool for marketers. This is probably why we have <a href="{$base_url}/blocs/email_marketing">more than 30 email marketing</a> vendors on ShareBloc. To narrow the list down, we identified 14 vendors that service a wide range of customer archetypes. While we understand that all the vendors listed here can serve multiple archetypes, we also recognize that some vendors can be better suited for different types of customers.
                            <p>For small and medium sized enterprises, consider <a href="{$base_url}/companies/activecampaign">ActiveCampaign</a>, <a href="{$base_url}/companies/aweber">AWeber</a>, <a href="{$base_url}/companies/campaigner">Campaigner</a>, <a href="{$base_url}/companies/constant_contact">Constant Contact</a>, <a href="{$base_url}/companies/icontact">iContact</a> (a subsidiary of <a href="{$base_url}/companies/vocus">Vocus</a>), <a href="{$base_url}/companies/mailchimp">MailChimp</a> and <a href="{$base_url}/companies/verticalresponse">VerticalResponse</a>. Of these vendors, there are some interesting differentiators. <a href="{$base_url}/companies/mailchimp">MailChimp</a>, for example, stresses its design-oriented chops. <a href="{$base_url}/companies/activecampaign">ActiveCampaign</a> focuses on its social media integration. <a href="{$base_url}/companies/verticalresponse">VerticalResponse</a> works well within the non-profit sector.
                        </div>

                        {* Activecampaign story *}
                        <div class="vendor_story_block">
                            <div class="vendor_story_logo"><img class="vendor_logo_story" src="{$base_url}/logos/activecampaign_v_thumb.jpg"></div>
                            <div class="vendor_story_title">CASE STUDY: <a class="vendor_story_link" href="{$base_url}/companies/activecampaign" >ACTIVECAMPAIGN</a></div>
                            <div class="vendor_story_text">
                                <p>ActiveCampaign combines all aspects of email marketing into a single & easy to use platform. Seamlessly create beautiful & engaging emails, send them to your segmented subscribers, and see what interactions & reactions occur in real time!
                            </div>
                            <div class="vendor_story_quote">
                                <p>&ldquo;TEST TEST TEST! Way too often email marketers get caught in the rut of sending the emails formatted the exact same way with only changing the content as needed. However, not all emails are the same and not all emails have the same goal. Try placing the call to action in a different place, changing the formatting, mixing up the balance of text vs. images.
                                <p>If you don't know where to even start making these types of changes ask these two questions:
                                <p class="p_with_indent">1. What is the goal of the email?
                                <p class="p_with_indent">2. What changes might help the goal succeed? Test variances from the answers you get.&rdquo;
                            </div>
                            <span class="vendor_story_author">&mdash; ADAM TUTTLE | PARTNER & BUSINESS DEVELOPMENT, <a class="vendor_story_link" href="{$base_url}/companies/activecampaign" >ACTIVECAMPAIGN</a></span>
                        </div>

                        <div class="chapter_text">
                            <p>For larger enterprises, consider <a href="{$base_url}/companies/campaign_monitor">Campaign Monitor</a> and <a href="{$base_url}/companies/exacttarget">ExactTarget</a>. While both vendors can work for small and medium sized businesses, both have built large businesses from clients like <a href="{$base_url}/companies/facebook">Facebook</a> and <a href="{$base_url}/companies/microsoft">Microsoft</a>.
                            <p>For marketers who are looking for a more design-oriented email tool, consider <a href="{$base_url}/companies/movable_ink">Movable Ink</a> and <a href="{$base_url}/companies/sendicate">Sendicate</a>. Both vendors apply modern design elements typically not found in email.
                        </div>

                        {* Sendicate story *}
                        <div class="vendor_story_block">
                            <div class="vendor_story_logo"><img class="vendor_logo_story" src="{$base_url}/logos/sendicate_v_thumb.jpg"></div>
                            <div class="vendor_story_title">CASE STUDY: <a class="vendor_story_link" href="{$base_url}/companies/sendicate" >SENDICATE</a></div>
                            <div class="vendor_story_text">
                                <p>Sendicate is an email app that brings together simplicity and design to simply send emails to people who matter.
                            </div>
                            <div class="vendor_story_quote">
                                <p>&ldquo;Make sure to have an email platform where content and messaging can take the focus. Content is king for email marketing and is just as important as other channels.  Don't lose focus of the message and your market just because emails can be so technical.&rdquo;
                            </div>
                            <span class="vendor_story_author">&mdash; CHAD JACKSON | CEO &amp; FOUNDER, <a class="vendor_story_link" href="{$base_url}/companies/sendicate" >SENDICATE</a></span>
                        </div>

                        <div class="chapter_text">
                            <p>Finally, for analytics-oriented email vendors, consider <a href="{$base_url}/companies/contactually">Contactually</a> and <a href="{$base_url}/companies/userfox">userfox</a>. Both <a href="{$base_url}/companies/contactually">Contactually</a> and <a href="{$base_url}/companies/userfox">userfox</a> use data to conduct a drip-marketing tactic through email. Depending on the context of your contacts or how your user interacts with your product, a well-executed drip-marketing campaign can lead to higher conversion rates.
                        </div>

                        {* Contactually story *}
                        <div class="vendor_story_block">
                            <div class="vendor_story_logo"><img class="vendor_logo_story" src="{$base_url}/logos/contactually_v_thumb.jpg"></div>
                            <div class="vendor_story_title">CASE STUDY: <a class="vendor_story_link" href="{$base_url}/companies/contactually" >CONTACTUALLY</a></div>
                            <div class="vendor_story_text">
                                <p>Contactually helps business professionals build better relationships. We make it easy to automatically convert your email contacts to one central spot, sync it with your CRM (if needed), and add tasks, follow-ups, and priority associated with those contacts. More importantly, we proactively suggest steps to take with your most important relationships.
                            </div>
                            <div class="vendor_story_quote">
                                <p>&ldquo;What Contactually solves is the problem of maintaining relationships with your network, which we know isn't always easy. The solution to your problem should allow you to be organic and systematically strengthen your network.&rdquo;
                            </div>
                            <span class="vendor_story_author">&mdash; TONY CAPPAERT | COO & CO-FOUNDER, <a class="vendor_story_link" href="{$base_url}/companies/contactually" >CONTACTUALLY</a></span>
                        </div>

                    </div>

                    {* SALES CALL SOFTWARE *}
                    <div class="chapter_title" id="chapter_title_sales">
                        SALES CALL SOFTWARE
                    </div>

                    <div class="chapter_content">

                        <div class="chapter_vendors">
                            {foreach from=$lead_farming_info.1.1.vendors key=v_id item=v}
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

                        <div class="chapter_text">
                            <p>What’s still true even in today’s world of text messages and tweets is that a phone call is still highly effective. We chose two vendors that take the traditional analog qualities of sales call into the digital world. Both take data from your sales calls and integrate other data coming from digital sources like email into your CRM.
                        </div>

                    </div>

                    {* OTHER ENGAGEMENT *}
                    <div class="chapter_title" id="chapter_title_other">
                        OTHER ENGAGEMENT
                    </div>

                    <div class="chapter_content">

                        <div class="chapter_vendors">
                            {foreach from=$lead_farming_info.1.2.vendors key=v_id item=v}
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

                        <div class="chapter_text">
                            <p>In addition to traditional email marketing and cold calling, there are group of vendors trying to do a few things differently in today’s age of social media and online video. For example, <a href="{$base_url}/companies/hubspot">HubSpot</a>, the leader in a new wave of inbound marketing, has helped thousands of marketing professionals better promote their products through blogs, landing pages and better SEO.
                        </div>

                        {* Ambassador story *}
                        <div class="vendor_story_block">
                            <div class="vendor_story_logo"><img class="vendor_logo_story" src="{$base_url}/logos/ambassador_v_thumb.jpg"></div>
                            <div class="vendor_story_title">CASE STUDY: <a class="vendor_story_link" href="{$base_url}/companies/ambassador" >AMBASSADOR</a></div>
                            <div class="vendor_story_text">
                                <p>Ambassador is social referral software for any size business. Ambassador's flexible, easy-to-use and fully integrated software provides your business with the tools to effectively engage, mobilize and reward your fans.
                            </div>
                            <div class="vendor_story_quote">
                                <p>&ldquo;What's changing is the use of data and analytics in the Marketing stack.  Decisions are being made based on measurable ROI, including via social.  Engagement with potential customers and users is critical; and leveraging ambassadors for referrals has never been more impactful than it is today.&rdquo;
                            </div>
                            <span class="vendor_story_author">&mdash; JEFF EPSTEIN | CHIEF AMBASSADOR, <a class="vendor_story_link" href="{$base_url}/companies/ambassador" >AMBASSADOR</a></span>
                        </div>

                    </div>

                </div>

                {* HARVESTING A LEAD *}
                <div id="chapter_title_harvest">
                    <div class="part_title">
                        <div class="part_title_image"><img src="/images/resources/harvesting.png"></div>
                        HARVESTING A LEAD
                    </div>
                    <div class="part_intro_text">
                        <p>Now that your leads are engaged, it’s time to take them through the end of the marketing funnel towards your sales team. These vendors all help bubble up leads that are ready to be harvested.
                    </div>

                    <div class="chapter_title" id="chapter_title_analyt">
                        LEAD ANALYTICS
                    </div>

                    <div class="chapter_content">

                        <div class="chapter_vendors">
                            {foreach from=$lead_farming_info.2.0.vendors key=v_id item=v}
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

                        <div class="chapter_text">
                            <p>Leads are only as good as the data that has qualified them. As Jeff Epstein from <a href="{$base_url}/companies/ambassador">Ambassador</a> previously noted, the rise of data and analytics to marketing has fundamentally changed how resources are allocated in the sales and marketing stack. Leads can be properly scored based on the effectiveness of a campaign or a channel, thanks to these data analytics vendors.
                        </div>

                        {* Bizible story *}
                        <div class="vendor_story_block">
                            <div class="vendor_story_logo"><img class="vendor_logo_story" src="{$base_url}/logos/bizible_v_thumb.jpg"></div>
                            <div class="vendor_story_title">CASE STUDY: <a class="vendor_story_link" href="{$base_url}/companies/bizible" >BIZIBLE</a></div>
                            <div class="vendor_story_text">
                                <p>Bizible’s patent-pending Marketing Analytics technology allows companies to accurately track any offline revenue back to the exact online marketing source. Bizible tracks customers from Google Search, AdWords, Social, Yelp, and beyond.
                            </div>
                            <div class="vendor_story_quote">
                                <p>&ldquo;Tracking calls and clicks is the status quo for marketers.  Unfortunately, calls and clicks are not a good proxy for revenue.  It's a smart move to invest in marketing analytics which extend the digital tracking trail to the closed sale.&rdquo;
                            </div>
                            <span class="vendor_story_author">&mdash; ANDY TURMAN | CO-FOUNDER + MARKETING, <a class="vendor_story_link" href="{$base_url}/companies/bizible" >BIZIBLE</a></span>
                        </div>

                        {* GoodData story *}
                        <div class="vendor_story_block">
                            <div class="vendor_story_logo"><img class="vendor_logo_story" src="{$base_url}/logos/gooddata_v_thumb.jpg"></div>
                            <div class="vendor_story_title">CASE STUDY: <a class="vendor_story_link" href="{$base_url}/companies/gooddata" >GOODDATA</a></div>
                            <div class="vendor_story_text">
                                <p>GoodData is a disruptive, cloud-based enterprise platform for business intelligence. The GoodData technology is intuitive, secure and fast. It helps convert big data into profitable insights and strategies for business executives.
                            </div>
                            <div class="vendor_story_quote">
                                <p>&ldquo;Don't get blindsided by a sudden lack of production by your marketing or lead development team! Use end-to-end analytics to deliver metrics that tell you immediately whether marketing is on track to deliver on its goals. And ensure your marketing KPIs map directly to the strategic success of your business.&rdquo;
                            </div>
                            <span class="vendor_story_author">&mdash; JOHN RODE | SENIOR DIRECTOR OF MARKETING, <a class="vendor_story_link" href="{$base_url}/companies/gooddata" >GOODDATA</a></span>
                        </div>

                    </div>

                    <div class="chapter_title" id="chapter_title_manage">
                        LEAD MANAGEMENT
                    </div>

                    <div class="chapter_content">

                        <div class="chapter_vendors">
                            {foreach from=$lead_farming_info.2.1.vendors key=v_id item=v}
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

                        <div class="chapter_text">
                            <p><a href="{$base_url}/blocs/lead_management">Lead Management</a> or Marketing Automation software is typically the central platform that all data is funneled into, where leads can be scored appropriately before they are triggered as a marketing qualified lead and sent to the sales team.
                            <p>Because lead management software is such a critical part of the modern marketing process, like email marketing, there are a lot of vendors in the space. We have taken some care in categorizing certain vendors based on their ideal customer archetype. Again, these vendors typically serve more than one archetype but if we had to choose just one, this is how we’d break it down.
                            <p>For larger enterprises, consider <a href="{$base_url}/companies/eloqua">Eloqua</a> and <a href="{$base_url}/companies/marketo">Marketo</a>. The two market leaders in the space have the robustness and the breadth of features and flexibility to service the largest enterprises. However, both offer small and medium sized business offerings.
                        </div>

                        {* Eloqua story *}
                        <div class="vendor_story_block">
                            <div class="vendor_story_logo"><img class="vendor_logo_story" src="{$base_url}/logos/eloqua_v_thumb.jpg"></div>
                            <div class="vendor_story_title">CASE STUDY: <a class="vendor_story_link" href="{$base_url}/companies/eloqua" >ELOQUA</a></div>
                            <div class="vendor_story_text">
                                <p>Eloqua helps clients dramatically accelerate revenue growth through Revenue Performance Management. Eloqua provides powerful business insight to inform marketing and sales decisions today that drive revenue growth tomorrow.
                            </div>
                            <div class="vendor_story_quote">
                                <p>&ldquo;For marketing and sales to be aligned, they need to measure themselves on a ‘common currency’ of an objectively qualified lead.  Once both agree on a definition of what behavior determines qualification, marketing can commit to sourcing specific numbers, and sales can commit to follow up speeds and close rates.
                                <p>Modern marketing is about understanding where each buyer is in their buying process and then delivering the right message at the right time based on that individual buyer’s interests.  To do this, you must reframe everything in terms of the buyer; what stages are in a buying process, what digital body language would indicate each stage, and what information they would next be interested in.&rdquo;
                            </div>
                            <span class="vendor_story_author">&mdash; STEVE WOODS, CTO &amp; CO-FOUNDER, <a class="vendor_story_link" href="{$base_url}/companies/eloqua" >ELOQUA</a></span>
                        </div>

                        <div class="chapter_text">
                            <p><a href="{$base_url}/companies/pardot">Pardot</a> and <a href="{$base_url}/companies/silverpop">Silverpop</a> are popular with a number of mid-sized businesses. <a href="{$base_url}/companies/pardot">Pardot</a>, is an <a href="{$base_url}/companies/exacttarget">ExactTarget</a>/<a href="{$base_url}/companies/salesforcecom">Salesforce.com</a> company and is increasingly moving towards larger enterprise. <a href="{$base_url}/companies/silverpop">Silverpop</a> plays well in the SME space but also has options for larger enterprise.
                                                        <p>Smaller businesses find that <a href="{$base_url}/companies/infusionsoft">Infusionsoft</a>, <a href="{$base_url}/companies/marketfish">Marketfish</a> and <a href="{$base_url}/companies/thrivehive">ThriveHive</a> fit their needs. All three vendors work well with enterprise and non-enterprise companies. <a href="{$base_url}/companies/marketfish">Marketfish</a>, in particular, has an automated marketplace solution for 3rd party record license and postal list rental.
                        </div>

                        <div class="chapter_protip">
                            <div class="chapter_protip_quotes">	&ldquo;</div>
                            <div class="chapter_protip_title">PRO-TIP</div>
                            <div class="chapter_protip_text">
                                <p>Integrating lead nurturing with your customer database allows you to value each potential customer and gives indications of where they are in their purchasing path. The biggest pitfall to marketing automation purchases is not dedicating the resources to properly integrate the package. Plan to keep a team dedicated to keep the product up to date and optimized to work with your ever-changing business processes.&rdquo;
                                <p><span class="chapter_protip_author">&mdash; KATYA CONSTANTINE</span> | FOUNDER, <a href="{$base_url}/companies/digishopgirl_media" class="chapter_protip_link">DIGISHOPGIRL MEDIA</a>
                            </div>
                        </div>

                    </div>
                </div>

                {* CONCLUSION *}
                <div>
                    <div class="part_title">CONCLUSION</div>
                    <div class="part_intro_text">
                        <p>Lead farming takes time and care. As marketers gain more distributed channels to acquire leads and more access to data on prospective leads, the signal to noise ratio becomes increasingly diluted.
                        <p>We believe we have identified 40 of the top vendors that empower marketers to manage this avalanche of data and provide the best scoring marketing qualified leads to their sales teams.
                    </div>

                    {* MENTIONED *}
                    <div class="chapter_title">
                        VENDORS MENTIONED&nbsp;&nbsp;<span class="simple_text_in_title">Sorted by taxonomy, then alphabetically</span>
                    </div>

                    <div class="chapter_content">
                        <table class="text_table">
                            <tr>
                                <td>
                                    <span class="table_subheader">IDENTIFY LEADS</span><br>
                                    {foreach from=$lead_farming_info.0.0.vendors key=v_id item=v}
                                        <a href="{$base_url}/companies/{$v.code_name}">{$v.vendor_name|capitalize}</a><br>
                                    {/foreach}
                                    <br>
                                    <span class="table_subheader">CONTEXTUALIZE LEADS</span><br>
                                    {foreach from=$lead_farming_info.0.1.vendors key=v_id item=v}
                                        <a href="{$base_url}/companies/{$v.code_name}">{$v.vendor_name|capitalize}</a><br>
                                    {/foreach}
                                    <br>
                                    <span class="table_subheader">SALES CALL SOFTWARE</span><br>
                                    {foreach from=$lead_farming_info.1.1.vendors key=v_id item=v}
                                        <a href="{$base_url}/companies/{$v.code_name}">{$v.vendor_name|capitalize}</a><br>
                                    {/foreach}
                                </td>
                                <td>
                                    <span class="table_subheader">LEAD ANALYTICS</span><br>
                                    {foreach from=$lead_farming_info.2.0.vendors key=v_id item=v}
                                        <a href="{$base_url}/companies/{$v.code_name}">{$v.vendor_name|capitalize}</a><br>
                                    {/foreach}
                                    <br>
                                    <span class="table_subheader">OTHER ENGAGEMENT</span><br>
                                    {foreach from=$lead_farming_info.1.2.vendors key=v_id item=v}
                                        <a href="{$base_url}/companies/{$v.code_name}">{$v.vendor_name|capitalize}</a><br>
                                    {/foreach}
                                    <br>
                                    <span class="table_subheader">LEAD MANAGEMENT</span><br>
                                    {foreach from=$lead_farming_info.2.1.vendors key=v_id item=v}
                                        <a href="{$base_url}/companies/{$v.code_name}">{$v.vendor_name|capitalize}</a><br>
                                    {/foreach}
                                </td>
                                <td>
                                    <span class="table_subheader">E-MAIL MARKETING</span><br>
                                    {foreach from=$lead_farming_info.1.0.vendors key=v_id item=v}
                                        <a href="{$base_url}/companies/{$v.code_name}">{$v.vendor_name|capitalize}</a><br>
                                    {/foreach}
                                    <br>
                                    We’d like to thank your graphic designer, <a href="http://jessicasuen.com/">Jessica Suen</a> for designing this infographic.
                                </td>
                            </tr>
                        </table>
                    </div>

                    {* DISCLAIMERS *}
                    <div class="chapter_title">
                        DISCLAIMERS
                    </div>
                    <div class="chapter_content">
                        <div class="chapter_text">
                            <p>Geographic Bias. Many of the vendors and pain points mentioned in this infographic are US-centric, particularly California-centric.  We follow the old adage of think globally but act locally and we hope we can service our customers far and abroad as well as we do our SF Bay Area colleagues.
                            <p>Vendor impartiality. We asked some vendors to articulate where they provide the biggest value to their customers.  Our request was that each quote was given with the end user in mind and to try not to be too commercial.  We realize that by highlighting certain vendors over others, we are giving some preferential treatment even when that is not our intent.  If you’re a vendor who would like to be showcased in future reports, contact us at vendors@sharebloc.com and we’ll get back to you shortly.  Thanks!
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div id="popups">
            {foreach from=$lead_farming_info key=column_id item=column}
                {foreach from=$column key=group_id item=group}
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
        {*if $dev_mode && $shouldUseCssRefresh*}
        <script src="/js/css_refresh.js" type="text/javascript"></script>
        {*/if*}
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
                $("#username_link").mouseover(function(){
                   $("#user_menu").removeClass('hidden');
                   $("#username_link").addClass('header_person_hover');
                });

                $("#user_menu_content").mouseleave(function(){
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

                                if (show_arr[code_name]===undefined) {
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
                var y =mouseY;
                var code_name = el.attr("data-codeName");

                //var q_num = el.attr("data-gID");
                //var is_left = q_num == 1 || q_num == 4 || q_num == 6;

                if (show_arr[code_name] <1 ) {
                    return false;
                }

                if (code_name==curr_popup) {
                    return false;
                }

                var popup_div = $("#popup_div_" + code_name);

                var popup_width = popup_div.width();
                var win_width = $(window).width();
                var right_distance = win_width - (mouseX + popup_width);
                var left_distance = mouseX - popup_width;

                var is_left = left_distance < right_distance;

                y = y+10;
                if (!is_left) {
                    x = x - popup_width;
                    x = x-15;
                } else {
                    x = x+15;
                }

                if (x<20) {
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
                if (show_arr[code_name] >0 ) {
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
