<!DOCTYPE html>
<html>
    <head>
        <title>ShareBloc - Seven hard questions asked by mobile developers</title>
        <meta name="keywords" content="business content, content discovery, enterprise, b2b, SMB, small medium business, lead-gen"/>
        <meta name="description" content="ShareBloc is a community of like-minded professionals who share, curate and discuss business content that matters.">
        <meta http-equiv="content-type" content="text/html; charset=utf-8">
        <link rel="shortcut icon" href="/favicon.png" type="image/x-icon">
        <link rel="stylesheet" href="css/infographics/splash-mobile.css" type="text/css" />
    </head>
    <body>
    <div class="download_block">
        <div class="download_link_div fixed">
                <div class="download_block_content">
                    <a href="{$index_page}"><img class="vslogo_header" alt="ShareBloc" src="/images/sharebloc_logo.png"></a>
            <a class="download_link fixed" href="/files/Seven_Questions_by_Mobile_Developers.pdf">Download the PDF</a>

                    {if isset($logged_in ) && $logged_in}
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
    {if $is_ie8}
        <div class="ie8_alert">Unfortunately, this page does not fully support IE with version less than 9 at the moment.</div>
        <img class="big_image_ie" width="100%" src="/images/mobilevendors-infographic_ie8.png">
    {else}
        <div class="block_container">
            <div class="block_content">
                <div class="block_text title">
                    <h1 class="page_title">
                        <div class="page_title_caps">SEVEN HARD QUESTIONS</div>
                        <br>
                        <div class="page_title_cursive">asked by</div>
                        <br>
                        <div class="page_title_caps">MOBILE DEVELOPERS</div>
                    </h1>
                </div>
            </div>
        </div>
        <div class="image_block_container">
            <div class="image_block_content">
                <div class="image_centered_block">
                    <div class="scaled_div">
                        <img class="big_image" src="/images/mobilevendors-infographic.png">
                        <div id="stuff_div">
                            {foreach from=$questions key=q_id item=q}
                                {foreach from=$q.vendors key=v_id item=v}
                                    <a href="/companies/{$v.code_name}">
                                        <img data-codeName="{$v.code_name}" data-qID="{$q_id}" class="vendor_icon {if $v.border}vendor_border_visible{else}vendor_border_hidden{/if}" id="q{$q_id}i{$v_id}" alt="{$v.vendor_name}" {if $v.local}src="/logos/{$v.logo_hash}_thumb.jpg"{else}src="{$base_url}/logos/{$v.logo_hash}_thumb.jpg"{/if}/>
                                    </a>
                                {/foreach}
                                <div class="bubble_link" data-qid="{$q.pdf_number}">
                                    <div class="bubble_text" id="q{$q_id}q">
                                        <div class="question_title">{$q.text_title}</div>
                                        <br>
                                        <div class="question_text">{$q.text_line1}<br>{$q.text_line2}</div>
                                    </div>
                                </div>
                            {/foreach}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {/if}
        <div class="block_container">
            <div class="block_content">
                <div class="block_text answers">
                    <p class="text_usual">
                        When we started building ShareBloc last summer, we knew that we could be an important resource
                        for <span class="text_important">founders, executives, product managers, and engineers</span> on vendors that solve their biggest pain
                        points. With that in mind, we released the <a class="text_link" href="http://blog.vendorstack.com/2012/10/06/the-top-50-vendors-used-by-startups/">“Top 50 Vendors Used by Startups”</a> last October at our private
                        beta launch. For our open beta launch, we are releasing ShareBloc’s second infographic, <span class="text_important">“Seven
                            Hard Questions Asked by Mobile Developers.”</span>
                    </p>
                    <p class="text_usual">
                        We interviewed 27 founders, developers, and vendors to identify “Seven Hard Questions Asked by Mobile
                        Developers.” as they scale their apps and arrived at the vendors who solve these pain points.
                    </p>
                    <a name="question_1"></a>
                    <div id="text_q_title1" class="text_q_title">1. HOW DO I GET MY APP TO RANK HIGHER ON THE APP STORES?</div>
                    <p class="text_vendors_mentioned">Vendors Mentioned: <a class="text_link" href="{$base_url}/companies/app_annie">App Annie</a>, <a class="text_link" href="{$base_url}/companies/appfigures">appFigures</a>, <a class="text_link" href="{$base_url}/companies/apptamin">Apptamin</a>, <a class="text_link" href="{$base_url}/companies/mobiledevhq">MobileDevHQ</a>, <a class="text_link" href="{$base_url}/companies/mopapp">Mopapp</a>, <a class="text_link" href="{$base_url}/companies/searchman">Searchman</a></p>
                    <p class="text_usual">Like search engine optimization, app store optimization (ASO) can help you organically acquire more users onto your
                        app store profile. There are a few vendors that help you better position your app in the app store and on the web.</p>

                    <p class="text_usual">Most developers we talked to didn’t naturally bring up ASO but when asked, realized they under-executed in this area.
                        As a starter, both <a class="text_link" href="{$base_url}/companies/searchman">Searchman</a> and <a class="text_link" href="{$base_url}/companies/mobiledevhq">MobileDevHQ</a> provide two good tutorials on ASO best practices. The key is to
                        think of ASO as SEO and focus on some key levers in your description to get better app discovery results: app name
                        (words and length), key words in the description, and good screenshots. But ASO is now going beyond just focusing
                        on your app store profile.</p>

                    <p class="text_usual">Ian Sefferman, CEO & Co-Founder from <a class="text_link" href="{$base_url}/companies/mobiledevhq">MobileDevHQ</a> says:</p>
                    <div class="text_q_quote">“We work with the largest app publishers and marketers. Amongst these
                        sophisticated marketers, we’re witnessing a change around app store optimization
                        and inbound app marketing. These app marketers increasingly realize that paid app
                        marketing leads to very high cost, unprofitable users -- and they need a solution.
                        At first, these app marketers thought inbound app marketing consisted of just app
                        store optimization and app store search, but now they recognize there are many
                        channels (such as search, social, earned media/PR, etc.), all of which work together
                        to drive profitable, engaged users to an app.”</div>
                    <p class="text_usual">There are also vendors who help you navigate the various rankings between the App Store, Google Play, and others.
                        Services like <a class="text_link" href="{$base_url}/companies/app_annie">App Annie</a>, <a class="text_link" href="{$base_url}/companies/mopapp">Mopapp</a>, and <a class="text_link" href="{$base_url}/companies/appfigures">appFigures</a> can provide you detailed app store analytics to help you figure
                        out which apps and platforms are more effective. <a class="text_link" href="{$base_url}/companies/apptamin">Apptamin</a> has a great comparison doc on the various options.</p>


                    <a name="question_2"></a>
                    <div id="text_q_title2" class="text_q_title">2. WHAT METRICS SHOULD I BE MEASURING ON MY APP?</div>
                    <p class="text_vendors_mentioned">Vendors Mentioned: <a class="text_link" href="{$base_url}/companies/flurry">Flurry</a>, <a class="text_link" href="{$base_url}/companies/gameanalytics">GameAnalytics</a>, <a class="text_link" href="{$base_url}/companies/google_analytics">Google Analytics</a>, <a class="text_link" href="{$base_url}/companies/keenio">Keen.io</a>, <a class="text_link" href="{$base_url}/companies/kontagent">Kontagent</a>, <a class="text_link" href="{$base_url}/companies/mixpanel">Mixpanel</a>, <a class="text_link" href="{$base_url}/companies/yozio">Yozio</a></p>
                    <p class="text_usual">As we interviewed developers on the metrics they track, the consistent messaging is that traditional “vanity” metrics
                        (e.g., installs) are not relevant and often, misleading. User retention, user engagement per session, and average revenue
                        per user (ARPU) are important but taken best when broken up by cohort.</p>
                    <p class="text_usual">Ultimately, all these metrics lead to lifetime value, which is a key performance indicator that spans across different
                        app categories. <a class="text_link" href="{$base_url}/companies/flurry">Flurry</a> and <a class="text_link" href="{$base_url}/companies/google_analytics">Google Analytics</a> are the market leaders in mobile analytics, but <a class="text_link" href="{$base_url}/companies/mixpanel">Mixpanel</a> and <a class="text_link" href="{$base_url}/companies/keenio">Keen.io</a>
                        are gaining traction within the startup community. Gaming app-makers prefer <a class="text_link" href="{$base_url}/companies/kontagent">Kontagent</a> and newcomer Game-
                        Analytics. <a class="text_link" href="{$base_url}/companies/kontagent">Kontagent</a> has a great speaker series on this.</p>
                    <p class="text_usual">Patrick Eggen from <a class="text_link" href="{$base_url}/companies/qualcomm_ventures">Qualcomm Ventures</a> says:</p>
                    <div class="text_q_quote">“Unfortunately we see many mobile start-ups embrace vanity metrics which inflate
                        and misrepresent their true performance. We extract this noise and focus on
                        meaningful metrics, which measure a loyal, engaged, and recurring user base.
                        Our more successful mobile app investments are led by data-driven management
                        teams who focus on real indicators of value as opposed to “empty calorie” metrics.”</div>
                    <p class="text_usual">Another pain point for mobile developers is identifying the customer acquisition costs, particularly since the app store
                        yields very little information about the user, particularly how the user arrived into installing your app. Newcomer
                        <a class="text_link" href="{$base_url}/companies/yozio">Yozio</a> helps mobile developers identify the source of the install so you can better measure channel ROI and K-factor.</p>
                    <a name="question_3"></a>
                    <div id="text_q_title3" class="text_q_title">3. WHAT TOOLS CAN HELP ME QA TEST QUICKLY ON ANDROID?</div>
                    <p class="text_vendors_mentioned">Vendors Mentioned: <a class="text_link" href="{$base_url}/companies/deviceanywhere">DeviceAnywhere</a>, <a class="text_link" href="{$base_url}/companies/perfecto_mobile">Perfecto Mobile</a>, <a class="text_link" href="{$base_url}/companies/testdroid">Testdroid</a></p>
                    <p class="text_usual">For some of our developers, Android testing is not an issue because 1. they haven’t built an Android app or 2. they
                        lack the resources to adequately test their app across multiple Android devices so they choose the most popular
                        form factors. For larger app developers or companies that deal with a wider user base, having an app perform as well
                        on a Galaxy S3 as a low-end Android phone is critical for user retention and satisfaction. Check out market leaders
                        <a class="text_link" href="{$base_url}/companies/deviceanywhere">DeviceAnywhere</a> and <a class="text_link" href="{$base_url}/companies/perfecto_mobile">Perfecto Mobile</a>, but also newcomer <a class="text_link" href="{$base_url}/companies/testdroid">Testdroid</a>.</p>
                    <p class="text_usual">Jeff Chen, head of business development at <a class="text_link" href="{$base_url}/companies/mshift">Mshift</a> says:</p>
                    <div class="text_q_quote">“MShift offers mobile banking solutions to banks and credit unions across the entire US.
                        As such, we’ve had to take a broad strokes approach to Android support. The key is to
                        maintain high visibility with your customers/end users on which Android platforms you
                        support. Thankfully, Google publishes OS and device metrics regularly and we base our
                        support off that. Once you have a punch-list of devices and OSes, vendors like <a class="text_link" href="{$base_url}/companies/deviceanywhere">DeviceAnywhere</a>
                        are very useful in getting you remote access to the device.”</div>

                    <a name="question_4"></a>
                    <div id="text_q_title4" class="text_q_title">4. HOW DO I GET OUR USERS TO RATE MY APP?</div>
                    <p class="text_vendors_mentioned">Vendors Mentioned: <a class="text_link" href="{$base_url}/companies/apptentive">Apptentive</a>, <a class="text_link" href="{$base_url}/companies/helpshift">Helpshift</a>, <a class="text_link" href="{$base_url}/companies/uservoice">UserVoice</a></p>
                    <p class="text_usual">High ratings are a strong leading indicator of organic installs. But how you ask your users to rate and review your app
                        is important. Vendors who provide great mobile customer service include <a class="text_link" href="{$base_url}/companies/apptentive">Apptentive</a>, <a class="text_link" href="{$base_url}/companies/helpshift">Helpshift</a>, and <a class="text_link" href="{$base_url}/companies/uservoice">UserVoice</a>.
                        Each vendor provides tools to better communicate with users, particularly when things don’t go according to plan.
                        The trick to good customer engagement is to create a fluid experience with the app so the user isn’t distracted from
                        the experience.</p>
                    <p class="text_usual">Most users will unlikely rate an app right away. <a class="text_link" href="{$base_url}/companies/apptentive">Apptentive</a>, for example, triggers in-app feedback and surveys
                        after certain user actions because an engaged user is more likely to be responsive to a rating request than one who
                        isn’t using the app frequently. Check out their blog on this.</p>
                    <p class="text_usual">Robi Ganguly, CEO & Co-Founder from <a class="text_link" href="{$base_url}/companies/apptentive">Apptentive</a>, says:</p>
                    <div class="text_q_quote">“The first step is realizing that many of your customers aren’t ready to rate or review your
                        app. Great ratings and reviews are the result of delivering an excellent app and delightful
                        experience. So, focus on determining how your customers feel about your app first. We
                        find that this approach enables companies to better understand their customers and then,
                        by making it easy for those who love their app to go rate, they end up with great reviews
                        and ratings.”</div>
                    <a name="question_5"></a>
                    <div id="text_q_title5" class="text_q_title">5. WHAT IS THE EFFECTIVENESS OF INCENTIVIZED INSTALLS?</div>
                    <p class="text_vendors_mentioned">Vendors Mentioned: <a class="text_link" href="{$base_url}/companies/appia">Appia</a>, <a class="text_link" href="{$base_url}/companies/admob">AdMob</a>, <a class="text_link" href="{$base_url}/companies/fiksu">Fiksu</a>, <a class="text_link" href="{$base_url}/companies/iad">iAd</a>, <a class="text_link" href="{$base_url}/companies/tapjoy">Tapjoy</a></p>
                    <p class="text_usual">The incentivized install (“incent”) is one tool for mobile developers to get installs from ad networks like <a class="text_link" href="{$base_url}/companies/tapjoy">Tapjoy</a>. We
                        talked to multiple developers and vendors and asked them about incents vs. non-incents vs. organic. A few key
                        insights emerged:</p>

                    <ol class="text_list">
                        <li>The cost of acquisition for incents vs. non-incents is dependent on the platform, app vertical, etc. But directionally,
                            incents are less costly than non-incents and require less upfront investment and resources to get started with. Each
                            incent and non-incent install counts equally in terms of iOS App Store rank improvement.
                        </li>
                        <li>The lifetime value for incents vs. non-incents vs. organic is also the same directionally. Incents are lower in value
                            than non-incents and both have significantly less value on average than organics.
                        </li>
                        <li>However, the value of any install on the App Store is equitable. This means while an incent is lower in LTV, any
                            given investment in incents typically has greater leverage on App Store rankings than non-incents because the
                            lower cost of incents allows you to buy more total installs. As the App Store heavily weighs download velocity, a
                            well timed incent campaign can lead to strong organic install generation during and after the paid campaign.
                        </li>
                    </ol>
                    <p class="text_usual">With all these options, it’s hard to differentiate the ROI for competitive ad networks like Apple <a class="text_link" href="{$base_url}/companies/iad">iAd</a>, Google <a class="text_link" href="{$base_url}/companies/admob">AdMob</a>
                        and <a class="text_link" href="{$base_url}/companies/appia">Appia</a> and the incentivized sources mentioned above.</p>

                    <p class="text_usual">Craig Pilli, VP of Business Development at <a class="text_link" href="{$base_url}/companies/fiksu">Fiksu</a>, explains:</p>
                    <div class="text_q_quote">“To effectively optimize mobile advertising, marketers must collect and compare all
                        cost and LTV data by source with an understanding of each sources impact on rank
                        and incremental organics. Such optimization efforts require an investment in building
                        in house infrastructures or the outsourcing of such efforts to a partner like <a class="text_link" href="{$base_url}/companies/fiksu">Fiksu</a>.”</div>
                    <p class="text_usual">For a fun infographic about mobile users, take a look at the latest from <a class="text_link" href="{$base_url}/companies/tapjoy">Tapjoy</a>.</p>

                    <a name="question_6"></a>
                    <div id="text_q_title6" class="text_q_title">6. WHO DO YOU USE FOR CRASH REPORTING?</div>
                    <p class="text_vendors_mentioned">Vendors Mentioned: <a class="text_link" href="{$base_url}/companies/bugsense">BugSense</a>, <a class="text_link" href="{$base_url}/companies/crashlytics">Crashlytics</a>, <a class="text_link" href="{$base_url}/companies/crittercism">Crittercism</a>, <a class="text_link" href="{$base_url}/companies/testflight">Testflight</a></p>
                    <p class="text_usual">We interviewed dozens of mobile developers and almost all of them used <a class="text_link" href="{$base_url}/companies/testflight">Testflight</a> at some point in their
                        development. For quick over-the-air beta testing, Testlight’s free service lets iOS developers deploy quickly. However,
                        as you start scaling into thousands and millions of users some of our developers have considered the three market
                        leaders in mobile crash reporting: <a class="text_link" href="{$base_url}/companies/bugsense">BugSense</a>, <a class="text_link" href="{$base_url}/companies/crashlytics">Crashlytics</a> and <a class="text_link" href="{$base_url}/companies/crittercism">Crittercism</a>.</p>
                    <p class="text_usual">Interestingly, the majority of users we talked to found the third party tools lacking so they all built their own crash
                        reporting tools. We admit that some of these app developers started building apps 3-4 years ago, before some of
                        the vendors were mainstream.</p>
                    <p class="text_usual">But the value of third party tools for crash reporting shouldn’t be understated, particularly as more apps are delivered
                        across multiple operating system releases, devices, app version updates, networks and connectivity. Twitter’s recent
                        acquisition of <a class="text_link" href="{$base_url}/companies/crashlytics">Crashlytics</a> is a strong indicator of the importance of identifying edge case bugs, particularly if you
                        consider the number of variables that enable a tweet from a mobile device somewhere in the world.</p>
                    <p class="text_usual"><a class="text_link" href="{$base_url}/companies/crittercism">Crittercism</a>, another leading vendor in what they call app performance management, is also a true believer. Since
                        launch, they’ve analyzed over 420 million devices and 30 billion app launches.</p>
                    <p class="text_usual">Andrew Levy, Co-Founder and CEO at <a class="text_link" href="{$base_url}/companies/crittercism">Crittercism</a> elaborates:</p>
                    <div class="text_q_quote">“We have customers ranging from indie developers to large global publishers. Having
                        this ubiquity is important because as apps scale globally across all mobile platforms and
                        devices, you need a vendor that can monitor not only the glaring coding mistakes but
                        the edge cases.”</div>

                    <a name="question_7"></a>
                    <div id="text_q_title7" class="text_q_title">7. WHAT SHOULD I KNOW BEFORE I DEVELOP ON A BACKEND-AS-A-SERVICE?</div>
                    <p class="text_vendors_mentioned">Vendors Mentioned: <a class="text_link" href="{$base_url}/companies/parse">Parse</a>, <a class="text_link" href="{$base_url}/companies/stackmob">StackMob</a>, <a class="text_link" href="{$base_url}/companies/urban_airship">Urban Airship</a></p>
                    <p class="text_usual">For developers who are looking to iterate quickly and scale across multiple platforms, using a backend-as-a-service
                        (BaaS) vendor can be a good choice. Vendors like <a class="text_link" href="{$base_url}/companies/urban_airship">Urban Airship</a>, <a class="text_link" href="{$base_url}/companies/parse">Parse</a>, and <a class="text_link" href="{$base_url}/companies/stackmob">StackMob</a> provide ready-made tools
                        for push notification, data analytics, social integration and development environments. Larger companies may use
                        mobile enterprise application platforms (MEAP), as coined by Gartner but none of our users considered them.</p>
                    <p class="text_usual">Almost all the users who used a BaaS started there through push, particularly <a class="text_link" href="{$base_url}/companies/urban_airship">Urban Airship</a>. Of the handful of users
                        we talked to that had scaled apps to millions of users, they all eventually built their own solution. Clearly, issues like
                        scale become a consideration. Others were also concerned about being “locked-in” with a singular provider. Having a
                        vendor that doesn’t lock you into a walled garden development environment is critical.</p>
                    <p class="text_usual">We asked Steve Gershik from <a class="text_link" href="{$base_url}/companies/stackmob">StackMob</a> what he tells his customers when they adopt <a class="text_link" href="{$base_url}/companies/stackmob">StackMob</a>:</p>
                    <div class="text_q_quote">“When considering a BaaS provider, there are three key factors to include in your research:
                        is their platform open, will it be flexible enough to meet your needs and scalable to grow
                        with your app.
                        An open platform allows developers to grow and extend their app quickly without being
                        at the whim of the providers’ development schedule. <a class="text_link" href="{$base_url}/companies/stackmob">StackMob</a> gives all customers the
                        ability to write custom server code to build their apps as they see fit. Our open and
                        documented API enables customers to create custom SDKs as well as revise our native
                        SDKs as their needs require.
                        Some BaaS providers are great for prototyping your app, but when you’re considering a
                        backend solution that will scale, Prototyping backend providers typically charge to use
                        their API, becoming increasingly expensive as API calls and users increase. When considering
                        a BaaS, <a class="text_link" href="{$base_url}/companies/stackmob">StackMob</a> offers its core API for free, so there is no “success tax” as you grow.”</div>

                    <div class="text_hr_container">
                        <div class="text_hr_div">
                            <hr class="text_hr">
                        </div>
                    </div>
                    <div class="mentioned_title">VENDORS MENTIONED (sorted by taxonomy then alphabetically)</div>
                    <table class="text_table">
                        <tr>
                            <td>
                                <span class="taxonomy_title">OPTIMIZATION</span><br>
                                <a class="text_link" href="{$base_url}/companies/app_annie">App Annie</a><br>
                                <a class="text_link" href="{$base_url}/companies/appfigures">appFigures</a><br>
                                <a class="text_link" href="{$base_url}/companies/apptamin">Apptamin</a><br>
                                <a class="text_link" href="{$base_url}/companies/mobiledevhq">MobileDevHQ</a><br>
                                <a class="text_link" href="{$base_url}/companies/mopapp">Mopapp</a><br>
                                <a class="text_link" href="{$base_url}/companies/searchman">Searchman</a><br>
                                <br>
                                <span class="taxonomy_title">ANALYTICS</span><br>
                                <a class="text_link" href="{$base_url}/companies/flurry">Flurry</a><br>
                                <a class="text_link" href="{$base_url}/companies/gameanalytics">GameAnalytics</a><br>
                                <a class="text_link" href="{$base_url}/companies/google_analytics">Google Analytics</a><br>
                                <a class="text_link" href="{$base_url}/companies/keenio">Keen.io</a><br>
                                <a class="text_link" href="{$base_url}/companies/kontagent">Kontagent</a><br>
                                <a class="text_link" href="{$base_url}/companies/mixpanel">Mixpanel</a><br>
                                <a class="text_link" href="{$base_url}/companies/yozio">Yozio</a><br>
                            </td>
                            <td>
                                <span class="taxonomy_title">QA TESTING</span><br>
                                <a class="text_link" href="{$base_url}/companies/deviceanywhere">DeviceAnywhere</a><br>
                                <a class="text_link" href="{$base_url}/companies/perfecto_mobile">Perfecto Mobile</a><br>
                                <a class="text_link" href="{$base_url}/companies/testdroid">Testdroid</a><br>
                                <br>
                                <span class="taxonomy_title">CUSTOMER SERVICE</span><br>
                                <a class="text_link" href="{$base_url}/companies/apptentive">Apptentive</a><br>
                                <a class="text_link" href="{$base_url}/companies/helpshift">Helpshift</a><br>
                                <a class="text_link" href="{$base_url}/companies/uservoice">UserVoice</a><br>
                                <br>
                                <span class="taxonomy_title">APP MONETIZATION</span><br>
                                <a class="text_link" href="{$base_url}/companies/appia">Appia</a><br>
                                <a class="text_link" href="{$base_url}/companies/admob">AdMob</a><br>
                                <a class="text_link" href="{$base_url}/companies/fiksu">Fiksu</a><br>
                                <a class="text_link" href="{$base_url}/companies/iad">iAd</a><br>
                                <a class="text_link" href="{$base_url}/companies/tapjoy">Tapjoy</a><br>
                            </td>
                            <td>
                                <span class="taxonomy_title">CRASH REPORTING</span><br>
                                <a class="text_link" href="{$base_url}/companies/bugsense">BugSense</a><br>
                                <a class="text_link" href="{$base_url}/companies/crashlytics">Crashlytics</a><br>
                                <a class="text_link" href="{$base_url}/companies/crittercism">Crittercism</a><br>
                                <a class="text_link" href="{$base_url}/companies/testflight">Testflight</a><br>
                                <br>
                                <span class="taxonomy_title">BACKEND-AS-A-SERVICE</span><br>
                                <a class="text_link" href="{$base_url}/companies/parse">Parse</a><br>
                                <a class="text_link" href="{$base_url}/companies/stackmob">StackMob</a><br>
                                <a class="text_link" href="{$base_url}/companies/urban_airship">Urban Airship</a><br>
                            </td>
                        </tr>

                    </table>

                    <p class="text_usual">ShareBloc would like to thank the executives from the following companies for participating in our
                        survey over the past few months.</p>
                    <table class="text_table">
                        <tr>
                            <td>
                                <a class="text_link" href="{$base_url}/companies/airbrite">Airbrite</a><br>
                                <a class="text_link" href="{$base_url}/companies/apptentive">Apptentive</a><br>
                                <a class="text_link" href="{$base_url}/companies/crittercism">Crittercism</a><br>
                                <a class="text_link" href="{$base_url}/companies/fiksu">Fiksu</a><br>
                                <a class="text_link" href="{$base_url}/companies/gratafy">Gratafy</a><br>
                                <a class="text_link" href="{$base_url}/companies/helpshift">Helpshift</a><br>
                                <a class="text_link" href="{$base_url}/companies/intro">Intro</a><br>
                                <a class="text_link" href="{$base_url}/companies/klamr">Klamr</a><br>
                                <a class="text_link" href="{$base_url}/companies/lolapps">LOLApps</a><br>
                            </td>
                            <td>
                                <a class="text_link" href="{$base_url}/companies/mobiledevhq">MobileDevHQ</a><br>
                                <a class="text_link" href="{$base_url}/companies/motion_math">Motion Math</a><br>
                                <a class="text_link" href="{$base_url}/companies/mshift">Mshift</a><br>
                                <a class="text_link" href="{$base_url}/companies/munkyfun">MunkyFun</a><br>
                                <a class="text_link" href="{$base_url}/companies/pingmegram">PingmeGram</a><br>
                                <a class="text_link" href="{$base_url}/companies/qualcomm_ventures">Qualcomm Ventures</a><br>
                                <span class="text_important">REConnect</span><br>
                                <a class="text_link" href="{$base_url}/companies/salsamobi">Salsamobi</a><br>
                                <a class="text_link" href="{$base_url}/companies/stackmob">StackMob</a><br>
                            </td>
                            <td>
                                <a class="text_link" href="{$base_url}/companies/swiftkey">Swiftkey</a><br>
                                <a class="text_link" href="{$base_url}/companies/tapfame">Tapfame</a><br>
                                <a class="text_link" href="{$base_url}/companies/tapgreet">TapGreet</a><br>
                                <a class="text_link" href="{$base_url}/companies/tapjoy">Tapjoy</a><br>
                                <span class="text_important">Third Track</span><br>
                                <a class="text_link" href="{$base_url}/companies/upstart_mobile">Upstart Mobile</a><br>
                                <a class="text_link" href="{$base_url}/companies/uservoice">UserVoice</a><br>
                                <a class="text_link" href="{$base_url}/companies/yozio">Yozio</a><br>
                                <a class="text_link" href="{$base_url}/companies/zynga">Zynga</a><br>
                            </td>
                        </tr>
                    </table>

                    <p class="text_usual">In addition, we would like to thank our designer Janice Kim (<a class="text_link" href="http://janice-kim.com/">janice-kim.com</a>) for designing the cool infographic.</p>
                    <p class="text_usual">As with the last infographic, here are some disclaimers:</p>
                    <div class="disclaimer_title">COMMERCIALLY-ORIENTED</div>
                    <p class="text_disclaimer">We realized very quickly that some of the hardest questions asked by mobile developers had nothing to do with
                        their vendors. For example, I cannot tell you how many times people were frustrated by the app store approval
                        process. The point of this infographic was to identify third-party vendors that could solve problems you would
                        otherwise have to build in-house or do by scratch.</p>
                    <div class="disclaimer_title">FOREST FOR TREES</div>
                    <p class="text_disclaimer">We took a wide brush to all the problems that plagued mobile developers. In future infographics and reports, we may
                        focus more on one particular sub-topic. For example, every developer we talked to had multiple questions and
                        opinions about monetization. To do the monetization question justice, for example, we would have to spend more
                        than one question and just a few vendor examples because the ecosystem is huge. We’ll save it for next time.</p>
                    <div class="disclaimer_title">VENDOR IMPARTIALITY</div>
                    <p class="text_disclaimer">We asked some vendors to articulate where they provide the biggest value to their customers. Our request was that
                        each quote was given with the end user in mind and to try not to be too commercial. We realize that by highlighting
                        certain vendors over others, we are giving some preferential treatment even when that is not our intent. If you’re a
                        vendor who would like to be showcased in future reports, contact us at <a class="text_link" href="mailto:vendors@sharebloc.com">vendors@sharebloc.com</a> and we’ll get
                        back to you shortly. Thanks!</p>

                </div>
            </div>
        </div>
        <div id="popups">
            {foreach from=$questions key=q_id item=q}
                {foreach from=$q.vendors key=v_id item=v}
                    <div data-codeName="{$v.code_name}" data-qID="{$q_id}" id="popup_div_{$v.code_name}" class="popup_div">
                        <div class="standard_popup">
                            <div class="vendor_icon_div">
                                <a href="/companies/{$v.code_name}"><img class="popup_vendor_icon" alt="{$v.vendor_name}" {if $v.local}src="/logos/{$v.logo_hash}_thumb.jpg"{else}src="{$base_url}/logos/{$v.logo_hash}_thumb.jpg"{/if}/></a>
                            </div>
                            <div class="popup_vendor_info">
                                <div class="popup_vendor_title"><a class="vendor_title_link" href="/companies/{$v.code_name}">{$v.vendor_name}</a></div>
                                <div class="vendor_tags">
                                    <a class="vendor_tag_link" href="/blocs/{$v.category.parent_tag_code_name}">{$v.category.parent_tag_name}</a>
                                    <span class="divider"> &middot; </span>
                                    <a class="vendor_tag_link" href="/blocs/{$v.category.code_name}">{$v.category.tag_name}</a>
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
        </div>
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
        <script type="text/javascript">
            var is_on_top = true;
        {if $is_ie8}
            $(document).ready(function() {
                $(".ie8_alert").fadeOut(10000);
            });
        {else}
            var curr_popup = false;
            var show_arr = { };
            var mouseX, mouseY;
            $(document).ready(function() {
                $(document).mousemove(function(e) {
                    mouseX = e.pageX;
                    mouseY = e.pageY;
                });

                $(window).resize(function() {
                    setImageScale();
                });

                $('.bubble_link').click(function() {
                    scrollToQuestion($(this).attr('data-qid'));
                    return false;
                });

                $('.vendor_icon').click(function() {
                    return true;
                });

                $('.vendor_icon, .popup_div').hover(
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

                setImageScale();
            });

            function showPopup(el) {
                var x = mouseX;
                var y =mouseY;
                var code_name = el.attr("data-codeName");

                var q_num = el.attr("data-qID");
                var is_left = q_num == 1 || q_num == 4 || q_num == 6;

                if (show_arr[code_name] <1 ) {
                    return false;
                }

                if (code_name==curr_popup) {
                    return false;
                }

                var popup_div = $("#popup_div_" + code_name);

                y = y+10;
                if (!is_left) {
                    var width = popup_div.width();
                    x = x - width;
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

            function scrollToQuestion(q_id) {
                var shift = $(window).height() / 3;
                var target = $("#text_q_title" + q_id);
                var scroll_to = target.offset().top - shift;
                $("html,body").animate({
                    "scrollTop": scroll_to
                }, "slow");
                return false;
            }

            function setImageScale() {
                $(".image_block_content").show();
                var width_needed = $("body").width();

                var width_real = $(".big_image").width();
                var height_real = $(".big_image").height();
                var scale = width_needed / width_real;

                if (scale > 0.48) {
                    scale = 0.5;
                }

                $(".scaled_div").css("-webkit-transform", "scale(" + scale + ")");
                $(".scaled_div").css("-ms-transform", "scale(" + scale + ")");
                $(".scaled_div").css("transform", "scale(" + scale + ")");

                $(".scaled_div").css("height", height_real * scale);

                $(".image_block_container").css("height", height_real * scale);
                $(".image_block_content").css("height", height_real * scale);

                $(".image_centered_block").css("height", height_real * scale);
                $(".image_centered_block").css("width", width_real * scale);

            }
        {/if}
            $(document).ready(function() {
                $("#username_link").mouseover(function(){
                   $("#user_menu").removeClass('hidden');
                   $("#username_link").addClass('header_person_hover');
                });

                $("#user_menu_content").mouseleave(function(){
                   $("#user_menu").addClass('hidden');
                   $("#username_link").removeClass('header_person_hover');
                });
                $(window).scroll(function() {
                    updateDownloadBlock();
                });
                updateDownloadBlock();
            });
            function updateDownloadBlock() {
                var currentOffset = $(window).scrollTop();
                if (currentOffset > 0 && is_on_top) {
                    is_on_top = false;
                    $(".fixed").removeClass("fixed").addClass("scroll");
                } else if (currentOffset===0 && !is_on_top) {
                    is_on_top = true;
                    $(".scroll").removeClass("scroll").addClass("fixed");
                }
            }

            var _gaq = _gaq || [];
            _gaq.push(['_setAccount', 'UA-29473234-1']);
            _gaq.push(['_setDomainName', 'vendorstack.com']);
            _gaq.push(['_trackPageview']);

            (function() {
                var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
                ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
            })();

        </script>
        {if !empty($download_now)}
        <script type="text/javascript">
        $dl_link = '{$download_now}';
        setTimeout(function() { $(location).attr('href',$dl_link); }, 1000);
        </script>
        {/if}
    </body>
</html>
