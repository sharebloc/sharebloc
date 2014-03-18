<!DOCTYPE html>
<html>
    <head>
        <title>ShareBloc - How to get your backoffice UP&amp;RUNNING</title>
        <meta name="keywords" content="business content, content discovery, enterprise, b2b, SMB, small medium business, lead-gen"/>
        <meta name="description" content="ShareBloc is a community of like-minded professionals who share, curate and discuss business content that matters.">
        <meta http-equiv="content-type" content="text/html; charset=utf-8">
        <link rel="shortcut icon" href="/favicon.png" type="image/x-icon">
        <link rel="stylesheet" href="css/infographics/splash-bo.css" type="text/css" />
    </head>
    <body>
        <div class="download_block">
            <div class="download_link_div fixed">
                <div class="download_block_content">
                    <a href="{$index_page}"><img class="vslogo_header" alt="ShareBloc" src="/images/sharebloc_logo.png"></a>
                <a class="download_link fixed" href="/files/Back_Office_Human_Resources.pdf">Download the PDF</a>

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
                <div class="color_block_virt_table">
                    <div class="left_block">
                        <div class="speck payroll_block">
                            <div class="speck_text"><span class="speck_title scroll_link" data-chid="payroll">PAYROLL</span></div>
                            <div class="speck_text">First thing you need to do is pay people.  But payroll is more than cutting checks; it’s also about worker’s comp, W-2s, PTO, and adhering to local, state, and federal tax filings, among others.</div>
                            <div class="speck_text speck_tip"><span class="protip">PRO TIP: </span>“Keep in mind set-up time, transparent pricing, and ease of use when selecting a provider. Many older services have hidden fees and require paper documents.”<span class="tip_author"> — Josh Reeves, <a class="text_link" href="{$base_url}/companies/ZenPayroll">ZenPayroll</a></span></div>
                            <div class="speck_text vendors_block">
                                <div class="consider">Consider these vendors:</div>
                                <ol>
                                    <li><a class="text_link" href="{$base_url}/companies/Intuit_Payroll">Intuit Payroll</a></li>
                                    <li><a class="text_link" href="{$base_url}/companies/Ovation_Payroll">Ovation Payroll</a></li>
                                    <li><a class="text_link" href="{$base_url}/companies/SurePayroll">SurePayroll</a></li>
                                    <li><a class="text_link" href="{$base_url}/companies/Wave">Wave Payroll</a></li>
                                    <li><a class="text_link" href="{$base_url}/companies/ZenPayroll">ZenPayroll</a></li>
                                </ol>
                            </div>
                            <div class="image_block image_payroll">
                                <img class="speck_image" src="/images/splash_bo_payroll.png">
                            </div>
                        </div>
                        <div class="speck health_block">
                            <div class="speck_text"><span class="speck_title scroll_link"  data-chid="health">HEALTHCARE & BENEFITS</span></div>
                            <div class="speck_text health_left_block">After cutting a paycheck, you’ll need to provide employees healthcare. Whether you use an intermediary or a one-stop shop, make sure you’re picking a provider that fits the needs of your team and scales appropriately.</div>
                            <div class="speck_text vendors_block">
                                <div class="consider">Consider these vendors:</div>
                                <ol>
                                    <li><a class="text_link" href="{$base_url}/companies/Benefitfocus">Benefitfocus</a></li>
                                    <li><a class="text_link" href="{$base_url}/companies/Maxwell_Health">Maxwell Health</a></li>
                                    <li><a class="text_link" href="{$base_url}/companies/Sherpaa">Sherpaa</a></li>
                                    <li><a class="text_link" href="{$base_url}/companies/SimplyInsured">SimplyInsured</a></li>
                                    <li><a class="text_link" href="{$base_url}/companies/Zenefits">Zenefits</a></li>
                                </ol>
                            </div>
                            <div class="image_block image_health">
                                <img class="speck_image" src="/images/splash_bo_health.png">
                            </div>
                        </div>
                        <div class="speck perf_block">
                            <div class="speck_text"><span class="speck_title scroll_link"  data-chid="perf">PERFORMANCE MANAGEMENT</span></div>
                            <div class="speck_text">With a larger headcount, you want to make sure your employees are happy and effective.  Perks like free lunches and Ping-Pong tables are nice, but an employee that is properly challenged and engaged is likely happier, and certainly better utilized.</div>
                            <div class="perf_tip_block">
                                <div class="speck_text speck_tip"><span class="protip">PRO TIP: </span>“Employee recognition and high-quality rewards improve employee productivity. Invest in your people!”<span class="tip_author"> — Fernando Campos, <a class="text_link" href="{$base_url}/companies/AnyPerk">AnyPerk</a></span></div>
                            </div>
                            <div class="perf_vendors_block">
                                <div class="speck_text vendors_block">
                                    <div class="consider">Consider these vendors:</div>
                                    <div class="half_ol_left">
                                        <ol>
                                            <li><a class="text_link" href="{$base_url}/companies/15Five">15Five</a></li>
                                            <li><a class="text_link" href="{$base_url}/companies/AnyPerk">AnyPerk</a></li>
                                            <li><a class="text_link" href="{$base_url}/companies/iDoneThis">iDoneThis</a></li>
                                        </ol>
                                    </div>
                                    <div class="half_ol_right">
                                        <ol start="4">
                                            <li><a class="text_link" href="{$base_url}/companies/Teamly">Teamly</a></li>
                                            <li><a class="text_link" href="{$base_url}/companies/Workcom">Work.com</a></li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                            <div class="image_block image_perf">
                                <img class="speck_image" src="/images/splash_bo_perf.png">
                            </div>
                        </div>
                    </div>
                    <div class="center_block">
                        {*                    <div class="speck title_block">
                        <div class="title_line line1">HOW TO GET YOUR</div>
                        <div class="title_line line2">BACK OFFICE</div>
                        <div class="title_line line3">UP & RUNNING</div>
                        <div class="title_line line4">PART 1: HUMAN RESOURCES</div>
                        <div class="image_block image_title">
                        <img class="speck_image" src="/images/splash_bo_ribbons.png">
                        </div>
                        </div>*}
                        <div class="center_title_block title_block">
                            <div class="image_title_block">
                                <img class="speck_image" src="/images/splash_bo_title.png">
                            </div>
                        </div>
                        <div class="center_text_block">
                            <div class="speck_text"><span class="speck_title scroll_link"  data-chid="strategy">CHOOSE YOUR STRATEGY</span></div>
                            <div class="speck_text">There are three main “strategies” to get your startup’s <a class="text_link" href="{$base_url}/blocs/human_resources">human resources</a> back-office up and running quickly. There are no wrong answers, just different preferences based on your company’s needs, bandwidth, and resources.</div>

                            <div class="center_speck_subheader scroll_link"  data-chid="doyourself">1. Do it yourself</div>
                            <div class="speck_text">You can use a selection of some of the latest and greatest cloud-based vendors in this infographic to help you do it yourself. If you need help managing them, try a HR Information System (HRIS) provider.</div>
                            <div class="speck_text speck_tip"><span class="protip">PRO-TIP:</span></span> “Automate day-to-day recordkeeping tasks in HR by leveraging technology so you can free up time for value-added and strategic HR functions.”<span class="tip_author"> — Dave Barnes, <a class="text_link" href="{$base_url}/companies/BambooHR">BambooHR</a></span></div>
                            <div class="speck_text"><span class="protip">HRIS VENDORS INCLUDE:</span> <a class="text_link" href="{$base_url}/companies/BambooHR">BambooHR</a>, <a class="text_link" href="{$base_url}/companies/Peoplefluent">Peoplefluent</a>, <a class="text_link" href="{$base_url}/companies/Silkroad">Silkroad</a>, <a class="text_link" href="{$base_url}/companies/TribeHR">TribeHR</a>, <a class="text_link" href="{$base_url}/companies/Workday">Workday</a></div>

                            <div class="center_speck_subheader scroll_link"  data-chid="inter">2. Intermediaries: Find a broker or MHRSP</div>
                            <div class="speck_text">There are a myriad of local and national brokers who can help you choose the right vendors and Managed HR Services Provider (MHRSP) who can even manage them for you.  As intermediaries, they profit from referral fees and/or management fees.
                            </div>
                            <div class="speck_text speck_tip">
                                <span class="protip">PRO-TIP:</span></span> “A MHRSP reduces possible errors, removes the fear of compliance, and the time spent away from building your business.”<span class="tip_author"> — Mark Goldstein, <a class="text_link" href="{$base_url}/companies/BackOps">BackOps</a></span>
                            </div>
                            <div class="speck_text speck_tip">
                                <span class="protip">PRO-TIP:</span></span> “A MHRSP can allow you to sync data across platforms from a variety of third party cloud technology partners, including non-HR services like accounting and finance.”<span class="tip_author"> — Ryan MacCarrigan, <a class="text_link" href="{$base_url}/companies/Advisor">Advisor</a></span>
                            </div>
                            <div class="speck_text">
                                <span class="protip">BROKERS INCLUDE:</span> <a class="text_link" href="{$base_url}/companies/Sweet__Baker">Sweet & Baker</a>, <a class="text_link" href="{$base_url}/companies/FundedBuy">FundedBuy</a>
                            </div>
                            <div class="speck_text">
                                <span class="protip">MHRSPs INCLUDE:</span> <a class="text_link" href="{$base_url}/companies/BackOps">BackOps</a>, <a class="text_link" href="{$base_url}/companies/Advisor">Advisor</a>
                            </div>

                            <div class="center_speck_subheader scroll_link"  data-chid="peoaso">3. One-Stop Shops: Use a PEO or ASO</div>
                            <div class="speck_text">
                                A <a class="text_link" href="http://en.wikipedia.org/wiki/Professional_employer_organization">Professional Employer Organization (PEO)</a> is a service provider that “hires” your employees so all the HR-related filings, including payroll, benefits, and compliance, fall under the PEO.  An <a class="text_link" href="http://en.wikipedia.org/wiki/Administrative_Services_Organization">Administrative Services Organization (ASO)</a> provides the same integrated one-stop shop solution, except that everything is kept under your company’s name. It is worth noting that while the HR software and services market is fragmented, one-stop shops command the largest share because of their ease of use.
                            </div>
                            <div class="speck_text speck_tip">
                                <span class="protip">PRO-TIP:</span></span> “An integrated solution provides you with the comfort of complete HR coverage and compliance while also allowing your employees to directly contact a single provider for all their HR questions.”<span class="tip_author"> — Brian Helmick, <a class="text_link" href="{$base_url}/companies/Algentis">Algentis</a></span>
                            </div>
                            <div class="speck_text">
                                <span class="protip">PEOs INCLUDE:</span> <a class="text_link" href="{$base_url}/companies/ADP">ADP TotalSource</a>, <a class="text_link" href="{$base_url}/companies/Insperity">Insperity</a>, <a class="text_link" href="{$base_url}/companies/Paychex">Paychex PEO</a>, <a class="text_link" href="{$base_url}/companies/VentureLoop">VentureLoop</a>, <a class="text_link" href="{$base_url}/companies/TriNet">TriNet</a>
                            </div>
                            <div class="speck_text">
                                <span class="protip">ASOs INCLUDE:</span> <a class="text_link" href="{$base_url}/companies/Algentis">Algentis</a>, <a class="text_link" href="{$base_url}/companies/Ceridian">Ceridian</a>, <a class="text_link" href="{$base_url}/companies/Paychex">Paychex ASO</a>
                            </div>
                        </div>
                    </div>
                    <div class="right_block">
                        <div class="speck hr_block">
                            <div class="speck_text"><span class="speck_title scroll_link"  data-chid="hr">HR COMPLIANCE</span></div>

                            <div class="speck_text">With more employees, it’s important to stay compliant.  There’s more to HR compliance than employee handbooks.  Make sure your office is compliant with local, state, and federal laws to limit your exposure.</div>

                            <div class="speck_text hr_tip_block speck_tip"><span class="protip">PRO TIP: </span>“The risk with compliance is as much about what you don't know as it is about complying with the things you do know.  Federal, state and local rules on everything from vacation to health care to maternity leave can cause major headaches for companies who unknowingly violate them.”<span class="tip_author"> — Jeremy McCarthy, <a class="text_link" href="{$base_url}/companies/VentureLoop">VentureLoop</a></span></div>
                            <div class="hr_right_block">
                                <div class="speck_text vendors_block">
                                    <div class="consider">Consider these vendors:</div>
                                    <div class="half_ol_left">
                                        <ol>
                                            <li><a class="text_link" href="{$base_url}/companies/Halogen_Software">Halogen</a></li>
                                            <li><a class="text_link" href="{$base_url}/companies/Silkroad">Silkroad</a></li>
                                            <li><a class="text_link" href="{$base_url}/companies/Simpler">Simpler</a></li>

                                        </ol>
                                    </div>
                                    <div class="half_ol_right">
                                        <ol start="4">
                                            <li><a class="text_link" href="{$base_url}/companies/Taleo">Taleo</a></li>
                                            <li><a class="text_link" href="{$base_url}/companies/Workday">Workday</a></li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                            <div class="image_block image_hr">
                                <img class="speck_image" src="/images/splash_bo_hr.png">
                            </div>
                        </div>
                        <div class="speck hiring_block">
                            <div class="speck_text"><span class="speck_title scroll_link"  data-chid="hiring">HIRING & RECRUITING</span></div>
                            <div class="speck_text">As you grow your company, recruiting becomes more than trolling LinkedIn and Craigslist.  Try one of the newer vendors infused with social and crowd-sourced functionality.</div>

                            <div class="speck_text speck_tip"><span class="protip">PRO TIP: </span>“Hiring can be incredibly challenging. Before you pay out big money for job boards and databases, ask your team for recommendations. Employee referrals are proven to get hired faster, stay longer and be a stronger cultural fit.”<span class="tip_author"> — Kes Thygesen, <a class="text_link" href="{$base_url}/companies/RolePoint">RolePoint</a></span></div>

                            <div class="speck_text hiring_tip2_block speck_tip"><span class="protip">PRO TIP: </span>“Applicants lie and SEO their own resumes to fit the job description even though their work, education, and skill set may not fit. Protect your company by pre-screening all applicants and running background checks.”<span class="tip_author"> — Adam Spector, <a class="text_link" href="{$base_url}/companies/Virtrue">Virtrue</a></span></div>

                            <div class="speck_text vendors_block hiring_vendors">
                                <div class="consider">Consider these vendors:</div>
                                <div class="half_ol_left">
                                    <ol>
                                        <li><a class="text_link" href="{$base_url}/companies/Developer_Auction">Developer Auction</a></li>
                                        <li><a class="text_link" href="{$base_url}/companies/Entelo">Entelo</a></li>
                                        <li><a class="text_link" href="{$base_url}/companies/GroupTalent">GroupTalent</a></li>
                                    </ol>
                                </div>
                                <div class="half_ol_right">
                                    <ol start="4">
                                        <li><a class="text_link" href="{$base_url}/companies/RolePoint">RolePoint</a></li>
                                        <li><a class="text_link" href="{$base_url}/companies/Virtrue">Virtrue</a></li>
                                    </ol>
                                </div>
                            </div>
                            <div class="image_block image_hiring">
                                <img class="speck_image" src="/images/splash_bo_hiring.png">
                            </div>
                        </div>

                        <div class="speck vslogo_block">
                            <div id="vslogo_div" class="image_vslogo_block">
                                <a href="{$base_url}/"><img class="speck_image" src="/images/vendorstack.png"></a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="block_container">
            <div class="block_content">
                <div class="addendum_intro">
                    <div class="addendum_word">ADDENDUM</div>
                    <p class="text_usual">As a new startup, <a class="text_link" href="{$base_url}">ShareBloc</a> is currently going through the process of choosing HR vendors to provide payroll, benefits and many other important HR functions to its growing number of employees. This infographic is a product of our research and numerous conversations with other fast growing companies and the vendors they use.</p>
                    <p class="text_usual">There are three main “strategies” to get your startup’s back-office up and running quickly. It’s a fair assumption that the more you pay, the less you have to manage (and see), which has its pros and cons. On one hand, you don’t have to deal with the non-core but critical functions associated with HR. On the other, you also pay a premium by not maximizing each function through a variety of vendors. There are no wrong answers, just different preferences based on your company’s needs, bandwidth and resources.
                </div>

                <div class="addendum_text">
                    <div id="chapter_title_doyourself" class="strategy_name">1. Do it yourself</div>
                    <p class="text_usual">You can use a selection of cloud-based vendors in this infographic to help you do it yourself. We broke out vendors based on different categories of HR services that almost all companies need. For this infographic, we focused mostly on cloud-based vendors that could be integrated with a wide variety of services, including HR Information System (HRIS) software or third-party service providers.
                    <p class="text_usual">If you decide to do it yourself, here are some things to watch out for:
                    <ul class="text_list">
                        <li><span class="question_bold">Does the vendor scale with us as we grow?</span> Some vendors charge a set-up fee and/or per employee fee. For a small company, limiting your up-front costs to per employee charges may make sense. However, as you grow in size, you may consider some of the larger vendors who can provide you bulk discounting.</li>
                        <li><span class="question_bold">Does the vendor scale geographically?</span> Some vendors do not have national or international coverage. This is particularly important when dealing with payroll, benefits and compliance.</li>
                        <li><span class="question_bold">Who do we call when something is broken?</span> Some cloud-based vendors do not have 24/7 support. This is particularly important if you have employees in different time zones and an emergency occurs.</li>
                        <li><span class="question_bold">Does the vendor play well with others?</span> The key to managing multiple vendors is to find ones that have open APIs and integrate with other popular services. You could also consider a HRIS provider as a central hub for your different vendors.</li>
                    </ul>
                    <p class="text_usual protip_text"><span class="protip">PRO-TIP:</span> “Automate day-to-day recordkeeping tasks in HR by leveraging technology so you can free up time for value-added and strategic HR functions.”<span class="tip_author"> — Dave Barnes, <a class="text_link" href="{$base_url}/companies/BambooHR">BambooHR</a></span>
                    <p class="text_usual"><span class="proscons">PROS:</span> It’s likely (but not guaranteed) that the pricing is better, particularly since many of the single-function vendors make their pricing transparent.
                    <p class="text_usual"><span class="proscons">CONS:</span> HR has many pitfalls related to opaque rules and compliance. Make sure your vendors scale with your company as you grow domestically and internationally.
                    <p class="text_usual proscons_text"><span class="proscons">HRIS VENDORS INCLUDE:</span> <a class="text_link" href="{$base_url}/companies/BambooHR">BambooHR</a>, <a class="text_link" href="{$base_url}/companies/Peoplefluent">Peoplefluent</a>, <a class="text_link" href="{$base_url}/companies/Silkroad">Silkroad</a>, <a class="text_link" href="{$base_url}/companies/TribeHR">TribeHR</a>, <a class="text_link" href="{$base_url}/companies/Workday">Workday</a>


                    <div  id="chapter_title_inter" class="strategy_name">1. Intermediaries: Find a broker or MHRSP</div>
                    <p class="text_usual">If you have a hard time selecting a vendor (even with <a class="green_link" href="{$base_url}">ShareBloc</a>), you can have a broker or Managed HR Services Provider (MHRSP) provide you referrals.
                    <p class="text_usual">Choosing a broker can be difficult because they have preferred vendors, which limit your options. A good broker will help you select the right vendor specifically tailored to your company’s needs and provide you with their broker discount.
                    <p class="text_usual">If you want your vendors externally managed as well, consider a MHRSP. As with brokers, your vendor selection is likely limited to preferred vendors. Because you are paying an intermediary to manage your HR vendors, you usually pay a premium for that service. Fortunately, when something doesn’t work, you also typically have only one person/company to contact.
                    <p class="text_usual">If you decide to choose an intermediary, here are some things to watch out for:
                    <ul class="text_list">
                        <li><span class="question_bold">What are you paying for?</span> Remember that these intermediaries are typically paid a commission by third party vendors for referrals. Not all MHRSPs take a commission so ask for some transparency on how pricing is broken down.</li>
                        <li><span class="question_bold">Does the intermediary scale geographically?</span> This is the same problem as choosing third party vendors. Some brokers or MHRSPs do not have coverage outside your state or region so you may have to find another intermediary to support you in a different location.</li>
                        <li><span class="question_bold">Does the intermediary have the right vendors?</span> Most intermediaries have their preferred third party HR vendors and will suggest them to your company. Sometimes this is a good thing because the intermediary has a discount rate or has been integrated into the MHRSP’s dashboard. Other times, it is with a vendor that is more costly/less effective than competitive offerings. Do your research on your preferred HR vendors beforehand to see if your intermediary offers them.</li>
                        <li><span class="question_bold">For MHRSPs only: How well does the MHRSP integrate their HR vendor partners?</span> If you’re going with a MHRSP, you’ll likely want to access many of your HR services through a centralized dashboard. Many cloud-based vendors have APIs that tie into proprietary or mainstream HRIS solutions.</li>
                    </ul>
                    <p class="text_usual protip_text"><span class="protip">PRO-TIP:</span> “A MHRSP reduces possible errors, removes the fear of compliance and the time spent away from building your business.”<span class="tip_author"> — Mark Goldstein, <a class="text_link" href="{$base_url}/companies/BackOps">BackOps</a></span>
                    <p class="text_usual protip_text"><span class="protip">PRO-TIP:</span> “A MHRSP can allow you to sync data across platforms from a variety of third party cloud technology partners, including non-HR services like accounting and finance.”<span class="tip_author"> — Ryan MacCarrigan, <a class="text_link" href="{$base_url}/companies/Advisor">Advisor</a></span>
                    <p class="text_usual"><span class="proscons">PROS:</span> You are paying for the convenience of someone else helping you find vendors and/or manage them for you. You also might have access to an intermediary discount.
                    <p class="text_usual"><span class="proscons">CONS:</span> Paying someone to do your work is more expensive. For some brokers and MHRSPs, you have to choose a vendor o their preferred list, which may limit your options.
                    <p class="text_usual proscons_text"><span class="proscons">BROKERS INCLUDE:</span> <a class="text_link" href="{$base_url}/companies/Sweet__Baker">Sweet & Baker</a>, <a class="text_link" href="{$base_url}/companies/FundedBuy">FundedBuy</a>
                    <p class="text_usual proscons_text"><span class="proscons">MHRSPs INCLUDE:</span> <a class="text_link" href="{$base_url}/companies/BackOps">BackOps</a>, <a class="text_link" href="{$base_url}/companies/Advisor">Advisor</a>

                    <div id="chapter_title_peoaso" class="strategy_name">3. One-Stop Shops: Use a PEO or ASO</div>
                    <p class="text_usual">The third strategy is to choose a third-party vendor that manages most of your HR functions with in-house solutions.
                    <p class="text_usual">The most popular type of vendor is a <a class="green_link" href="http://en.wikipedia.org/wiki/Professional_employer_organization">Professional Employer Organization (PEO)</a>. A PEO “hires” your employees so all the HR-related filings, including payroll, benefits, and compliance fall under the PEO. PEOs may provide group discounts typically offered only to larger companies.
                    <p class="text_usual">The other type of one-stop shop is an <a class="green_link" href="http://en.wikipedia.org/wiki/Administrative_Services_Organization">Administrative Services Organization (ASO)</a>. ASOs have typically the same functions as a PEO but your company does not fall under their tax umbrella. By retaining your tax identity, your company is at risk for employment related liabilities but you retain your company’s identity on your paystubs and W-2s.
                    <p class="text_usual">It is worth noting while the HR software/services market is fragmented, one-stop shops command the largest share because of their ease of use.
                    <p class="text_usual">If you decide to choose a one-stop shop, here are some things to watch out for:
                    <ul class="text_list">
                        <li><span class="question_bold">What are you paying for?</span> More than any other strategy, one-stop shop fees are hidden in a single price point, which is typically not itemized so you cannot price compare. Most PEOs and ASOs offer different pricing for companies of different sizes so be sure to ask questions on what the costs looks like with a headcount of 5 vs. 50.</li>
                        <li><span class="question_bold">Is the one-stop shop price competitive for single location companies?</span> Many PEOs and ASOs are national, which is great for companies that have multiple employees around the country. However, for many startups or small businesses with only one location, a national provider may be more expensive, particularly when it comes to customer service or pricing. For example, some PEOs have very diversified clients with different medical benefits requirements. If you are a three-person startup with all employees in their mid-20s, you may not want to buy using a group discount that includes a 500-person company with a more diversified employee base.</li>
                        <li><span class="question_bold">For PEOs only: Does the PEO protect me against HR liabilities?</span> One of the compelling reasons to go with a PEO is that they manage all employment related risks, including delicate issues like sexual harassment or employment disputes. Most PEOs will say they cover these risks but ask for some details, including examples of prior coverage.</li>
                    </ul>
                    <p class="text_usual protip_text"><span class="protip">PRO-TIP:</span> “An integrated solution provides you with the comfort of complete HR coverage and compliance while also allowing your employees to directly contact a single provider for all their HR questions.”<span class="tip_author"> — Brian Helmick, <a class="text_link" href="{$base_url}/companies/Algentis">Algentis</a></span>
                    <p class="text_usual"><span class="proscons">PROS:</span> You may get some discount pricing from being bundled with other small-medium sized companies. PEOs also claim to protect your organization from HR compliance exposure.
                    <p class="text_usual"><span class="proscons">CONS:</span> This is typically the most expensive option because you are outsourcing the entire HR department. Pricing tends to be not itemized so it’s hard to price compare.
                    <p class="text_usual proscons_text"><span class="proscons">PEOs INCLUDE:</span> <a class="text_link" href="{$base_url}/companies/ADP">ADP TotalSource</a>, <a class="text_link" href="{$base_url}/companies/Insperity">Insperity</a>, <a class="text_link" href="{$base_url}/companies/Paychex">Paychex PEO</a>, <a class="text_link" href="{$base_url}/companies/VentureLoop">VentureLoop</a>, <a class="text_link" href="{$base_url}/companies/TriNet">TriNet</a>
                    <p class="text_usual proscons_text"><span class="proscons">ASOs INCLUDE:</span> <a class="text_link" href="{$base_url}/companies/Algentis">Algentis</a>, <a class="text_link" href="{$base_url}/companies/Ceridian">Ceridian</a>, <a class="text_link" href="{$base_url}/companies/Paychex">Paychex ASO</a>
                    <p class="text_usual">If you decide to do it yourself, consider the following new cloud-based vendors for each critical HR function.


                    <div id="chapter_title_payroll" class="function_title"><a href="{$base_url}/blocs/payroll">PAYROLL</a></div>
                    <p class="text_usual consider_text">Consider these vendors: <a class="text_link" href="{$base_url}/companies/Intuit_Payroll">Intuit Payroll</a>, <a class="text_link" href="{$base_url}/companies/Ovation_Payroll">Ovation Payroll</a>, <a class="text_link" href="{$base_url}/companies/SurePayroll">SurePayroll</a>, <a class="text_link" href="{$base_url}/companies/Wave">Wave Payroll</a>, <a class="text_link" href="{$base_url}/companies/ZenPayroll">ZenPayroll</a>.
                    <p class="text_usual protip_text"><span class="protip">PRO-TIP:</span> “Keep in mind set-up time, transparent pricing, and ease of use when selecting a provider. Many older services have hidden fees and require paper documents. A modern payroll service is paperless, can be set-up in minutes online, and can be used on any device, whether it's a computer, laptop, tablet or smartphone."<span class="tip_author"> — Josh Reeves, <a class="text_link" href="{$base_url}/companies/ZenPayroll">ZenPayroll</a></span>
                    <p class="text_usual">If you decide to choose a cloud-based <a class="green_link" href="{$base_url}/blocs/payroll">payroll</a> provider, here are some things to watch out for:
                    <ul class="text_list">
                        <li><span class="question_bold">What is the price per employee?</span> If a cloud-based payroll provider doesn’t provide transparent pricing upfront, it’s likely there are some hidden costs. Of course, as you grow in size, payroll per employee doesn’t scale and you should find group discount pricing.</li>
                        <li><span class="question_bold">What are you paying for?</span> Many small businesses still do payroll by hand or through desktop software like QuickBooks and are likely filing their tax forms independently. Some vendors like <a class="text_link green_link" href="{$base_url}/companies/Wave">Wave Payroll</a> or <a class="text_link green_link" href="{$base_url}/companies/Intuit_Payroll">Intuit Basic Payroll</a> provide a similar limited cloud offering.</li>
                    </ul>
                    <div id="chapter_title_health" class="function_title"><a href="{$base_url}/blocs/healthcare__benefits">HEALTHCARE & BENEFITS</a></div>
                    <p class="text_usual consider_text">Consider these vendors: <a class="text_link" href="{$base_url}/companies/Benefitfocus">Benefitfocus</a>, <a class="text_link" href="{$base_url}/companies/Maxwell_Health">Maxwell Health</a>, <a class="text_link" href="{$base_url}/companies/Sherpaa">Sherpaa</a>, <a class="text_link" href="{$base_url}/companies/SimplyInsured">SimplyInsured</a>, <a class="text_link" href="{$base_url}/companies/Zenefits">Zenefits</a>.
                    <p class="text_usual">After cutting a paycheck, you’ll need to provide employees healthcare. Whether you use an intermediary or a one-stop shop, make sure you’re picking a provider that fits the needs of your team and scales appropriately.
                    <p class="text_usual">If you are going through a broker intermediary, it’s worth noting that brokers (online or offline) take a commission <a class="green_link" href="http://www.hschange.com/CONTENT/480/">(2-8% is often quoted)</a> for each provider they recommend.
                    <p class="text_usual">If you are using a one-stop shop, you may benefit from large company discounts for your company.
                    <p class="text_usual">Of course, all this may change in 2014 when <a class="green_link" href="http://www.healthcare.gov/marketplace/small-businesses/index.html">Obamacare’s health insurance exchange for small businesses or Small Business Health Options Program (SHOP)</a> is introduced. <a class="green_link" href="http://www.healthcare.gov/news/factsheets/2011/07/exchanges07112011c.html">More information about SHOPs are coming in October 2013</a>.
                    <p class="text_usual">If you decide to choose a cloud-based <a class="green_link" href="{$base_url}/blocs/healthcare__benefits">healthcare & benefits</a> provider, here are some things to watch out for:
                    <ul class="text_list">
                        <li><span class="question_bold">Does the broker scale with your company?</span> Many of these online exchanges act as the “Kayak” of healthcare insurance providers. However, each state and region has different providers. As you grow your company to different locations, make sure your provider has out of state options.</li>
                    </ul>
                    <div id="chapter_title_hiring" class="function_title"><a href="{$base_url}/blocs/recruiters">HIRING & RECRUITING</a></div>
                    <p class="text_usual consider_text">Consider these vendors: <a class="text_link" href="{$base_url}/companies/Developer_Auction">Developer Auction</a>, <a class="text_link" href="{$base_url}/companies/Entelo">Entelo</a>, <a class="text_link" href="{$base_url}/companies/GroupTalent">GroupTalent</a>, <a class="text_link" href="{$base_url}/companies/RolePoint">RolePoint</a>, <a class="text_link" href="{$base_url}/companies/Virtrue">Virtrue</a>
                    <p class="text_usual">As you grow your company, recruiting becomes more than trolling LinkedIn and Craigslist. Try one of the newer vendors infused with social and crowd-sourced functionality.
                    <p class="text_usual protip_text"><span class="protip">PRO-TIP:</span> “Hiring can be incredibly challenging. Before you pay out big money for job-boards and databases, ask your team for recommendations — employee referrals are proven to get hired faster, stay longer and be a stronger cultural fit.”<span class="tip_author"> — Kes Thygesen, <a class="text_link" href="{$base_url}/companies/RolePoint">RolePoint</a></span>
                    <p class="text_usual protip_text"><span class="protip">PRO-TIP:</span> “Applicants lie and SEO their own resumes to fit the job description even though their work, education, and skill set may not fit. Protect your company by pre-screening all applicants and running background checks.”<span class="tip_author"> — Adam Spector, <a class="text_link" href="{$base_url}/companies/Virtrue">Virtrue</a></span>
                    <p class="text_usual">If you decide to choose a cloud-based <a class="green_link" href="{$base_url}/blocs/recruiters">hiring and recruiting</a> provider, here are some things to watch out for:
                    <ul class="text_list">
                        <li><span class="question_bold">What are you paying for?</span> Instead of paying for a job board, try working with a vendor that identifies candidates through your social network or an open marketplace. This way, you only end up paying for a successful hire. They are also typically more price-competitive compared to a traditional offline recruiter.</li>
                    </ul>
                    <div id="chapter_title_hr" class="function_title"><a href="{$base_url}/blocs/hr_services__tools">HR COMPLIANCE</a></div>
                    <p class="text_usual consider_text">Consider these vendors: <a class="text_link" href="{$base_url}/companies/Halogen_Software">Halogen Software</a>, <a class="text_link" href="{$base_url}/companies/Silkroad">Silkroad</a>, <a class="text_link" href="{$base_url}/companies/Simpler">Simpler</a>, <a class="text_link" href="{$base_url}/companies/Taleo">Taleo</a>, <a class="text_link" href="{$base_url}/companies/Workday">Workday</a>.
                    <p class="text_usual">With more employees, it’s important to stay compliant. There’s more to HR compliance than employee handbooks. Make sure your office is compliant with local, state, and federal laws to limit your exposure.
                    <p class="text_usual protip_text"><span class="protip">PRO-TIP:</span> “The risk with compliance is as much about what you don't know as it is about complying with the things you do know. Federal, state and local rules on everything from vacation to health care to maternity leave can cause major headaches for companies who unknowingly violate them.”<span class="tip_author"> — Jeremy McCarthy, <a class="text_link" href="{$base_url}/companies/VentureLoop">VentureLoop</a></span>
                    <p class="text_usual">If you decide to choose a cloud-based <a class="green_link" href="{$base_url}/blocs/hr_services__tools">HR Compliance</a> provider, here are some things to watch out for:
                    <ul class="text_list">
                        <li><span class="question_bold">Do I need a vendor for employee onboarding?</span> The answer is yes and no. If you are already working with a MHRSP or one-stop shop, they will likely handling all this for your company. If you plan on doing everything on your own, you should consider an employee onboarding software provider like the ones listed above.</li>
                    </ul>
                    <div id="chapter_title_perf" class="function_title"><a href="{$base_url}/blocs/employee_performance">PERFORMANCE MANAGEMENT</a></div>
                    <p class="text_usual consider_text">Consider these vendors: <a class="text_link" href="{$base_url}/companies/15Five">15Five</a>, <a class="text_link" href="{$base_url}/companies/AnyPerk">AnyPerk</a>, <a class="text_link" href="{$base_url}/companies/iDoneThis">iDoneThis</a>, <a class="text_link" href="{$base_url}/companies/Teamly">Teamly</a>, <a class="text_link" href="{$base_url}/companies/Workcom">Work.com</a>
                    <p class="text_usual">With a larger headcount, you want to make sure your employees are happy and effective. Perks like free lunches and Ping-Pong tables are nice, but an employee that is properly challenged and engaged is likely happier, and certainly better utilized.
                    <p class="text_usual protip_text"><span class="protip">PRO-TIP:</span> “Employee recognition and high-quality rewards improve employee productivity. Invest in your people!”<span class="tip_author"> — Fernando Campos, <a class="text_link" href="{$base_url}/companies/AnyPerk">AnyPerk</a></span>
                    <p class="text_usual">If you decide to choose a cloud-based <a class="green_link" href="{$base_url}/blocs/employee_performance">Performance Management</a> provider, here are some things to watch out for:
                    <ul class="text_list">
                        <li><span class="question_bold">How can I successfully integrate performance management tools in our company?</span> You want to make reporting and record tracking part of the daily experience. If your employees are engaging your vendor on a frequent basis, their own weekly and monthly progress will drive performance.</li>
                    </ul>
                    <div class="text_hr_container">
                        <div class="text_hr_div">
                            <hr class="text_hr">
                        </div>
                    </div>
                    <div class="mentioned_title">VENDORS MENTIONED</div>
                    <p class="text_usual">Sorted by taxonomy, then alphabetically</p>

                    <table class="text_table">
                        <tr>
                            <td>
                                <span class="table_subheader">HRIS</span><br>
                                <a class="text_link" href="{$base_url}/companies/BambooHR">BambooHR</a><br>
                                <a class="text_link" href="{$base_url}/companies/Peoplefluent">Peoplefluent</a><br>
                                <a class="text_link" href="{$base_url}/companies/Silkroad">Silkroad</a><br>
                                <a class="text_link" href="{$base_url}/companies/TribeHR">TribeHR</a><br>
                                <a class="text_link" href="{$base_url}/companies/Workday">Workday</a><br>
                                <br>
                                <span class="table_subheader">Intermediaries</span><br>
                                <a class="text_link" href="{$base_url}/companies/Advisor">Advisor</a><br>
                                <a class="text_link" href="{$base_url}/companies/BackOps">BackOps</a><br>
                                <a class="text_link" href="{$base_url}/companies/FundedBuy">FundedBuy</a><br>
                                <a class="text_link" href="{$base_url}/companies/Sweet__Baker">Sweet & Baker</a><br>
                                <br>
                                <span class="table_subheader">One-Stop Shops</span><br>
                                <a class="text_link" href="{$base_url}/companies/ADP">ADP TotalSource</a><br>
                                <a class="text_link" href="{$base_url}/companies/Algentis">Algentis</a><br>
                                <a class="text_link" href="{$base_url}/companies/Ceridian">Ceridian</a><br>
                                <a class="text_link" href="{$base_url}/companies/Insperity">Insperity</a><br>
                                <a class="text_link" href="{$base_url}/companies/Paychex">Paychex ASO</a><br>
                                <a class="text_link" href="{$base_url}/companies/Paychex">Paychex PEO</a><br>
                                <a class="text_link" href="{$base_url}/companies/TriNet">TriNet</a><br>
                                <a class="text_link" href="{$base_url}/companies/VentureLoop">VentureLoop</a><br>
                            </td>
                            <td>
                                <span class="table_subheader">Payroll</span><br>
                                <a class="text_link" href="{$base_url}/companies/Intuit_Payroll">Intuit Payroll</a><br>
                                <a class="text_link" href="{$base_url}/companies/Ovation_Payroll">Ovation Payroll</a><br>
                                <a class="text_link" href="{$base_url}/companies/SurePayroll">SurePayroll</a><br>
                                <a class="text_link" href="{$base_url}/companies/Wave">Wave Payroll</a><br>
                                <a class="text_link" href="{$base_url}/companies/ZenPayroll">ZenPayroll</a><br>
                                <br>
                                <span class="table_subheader">Healthcare & Benefits</span><br>
                                <a class="text_link" href="{$base_url}/companies/Benefitfocus">Benefitfocus</a><br>
                                <a class="text_link" href="{$base_url}/companies/Maxwell_Health">Maxwell Health</a><br>
                                <a class="text_link" href="{$base_url}/companies/Sherpaa">Sherpaa</a><br>
                                <a class="text_link" href="{$base_url}/companies/SimplyInsured">SimplyInsured</a><br>
                                <a class="text_link" href="{$base_url}/companies/Zenefits">Zenefits</a><br>
                                <br>
                                <span class="table_subheader">Hiring & Recruiting</span><br>
                                <a class="text_link" href="{$base_url}/companies/Developer_Auction">Developer Auction</a><br>
                                <a class="text_link" href="{$base_url}/companies/Entelo">Entelo</a><br>
                                <a class="text_link" href="{$base_url}/companies/GroupTalent">GroupTalent</a><br>
                                <a class="text_link" href="{$base_url}/companies/RolePoint">RolePoint</a><br>
                                <a class="text_link" href="{$base_url}/companies/Virtrue">Virtrue</a><br>
                            </td>
                            <td>
                                <span class="table_subheader">HR Compliance</span><br>
                                <a class="text_link" href="{$base_url}/companies/Halogen_Software">Halogen Software</a><br>
                                <a class="text_link" href="{$base_url}/companies/Silkroad">Silkroad</a><br>
                                <a class="text_link" href="{$base_url}/companies/Simpler">Simpler</a><br>
                                <a class="text_link" href="{$base_url}/companies/Taleo">Taleo</a><br>
                                <a class="text_link" href="{$base_url}/companies/Workday">Workday</a><br>
                                <br>
                                <span class="table_subheader">Performance Management</span><br>
                                <a class="text_link" href="{$base_url}/companies/15Five">15Five</a><br>
                                <a class="text_link" href="{$base_url}/companies/AnyPerk">AnyPerk</a><br>
                                <a class="text_link" href="{$base_url}/companies/iDoneThis">iDoneThis</a><br>
                                <a class="text_link" href="{$base_url}/companies/Teamly">Teamly</a><br>
                                <a class="text_link" href="{$base_url}/companies/Workcom">Work.com</a><br>
                            </td>
                        </tr>
                    </table>
                    <div class="text_usual text_thanks">We’d like to thank our graphic designer <a class="green_link" href="http://cargocollective.com/jessicasuen/">Jessica Suen</a> for designing this infographic.</div>
                    <div class="disclaimer_title">Disclaimers</div>
                    <p class="text_usual"><span class="question_bold">Geographic Bias.</span> Many of the vendors and pain points mentioned in this infographic are US-centric, particularly California-centric. We follow the old adage of think globally but act locally and we hope we can service our customers far and abroad as well as we do our SF Bay Area colleagues. Remember, just because we didn’t cover your local broker or PEO, doesn’t mean the pro-tips don’t apply!
                    <p class="text_usual"><span class="question_bold">Vendor impartiality.</span> We asked some vendors to articulate where they provide the biggest value to their customers. Our request was that each quote was given with the end user in mind and to try not to be too commercial. We realize that by highlighting certain vendors over others, we are giving some preferential treatment even when that is not our intent. If you’re a vendor who would like to be showcased in future reports, contact us at <a class="mailto_link" href="mailto:vendors@sharebloc.com">vendors@sharebloc.com</a> and we’ll get back to you shortly. Thanks!
                </div>
            </div>
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
            $(document).ready(function() {
                $(window).scroll(function() {
                    updateDownloadBlock();
                });
                $(window).resize(function() {
                    setTimeout("alignBlocks()", 100);
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

                updateDownloadBlock();
                setTimeout("alignBlocks()", 100);
            });

            function alignBlocks() {

                if ($(".color_block_virt_table").css("display")==='table') {
                    $(".vslogo_block").height("auto");
                    $(".perf_block").height("auto");
                    return false;
                }
                var left_height = $(".left_block").outerHeight(true);
                var center_height = $(".center_block").outerHeight(true);
                var right_height = $(".right_block").outerHeight(true);
                var vslogo_height = $(".vslogo_block").outerHeight(true);
                var perf_height = $(".perf_block").outerHeight(true);

                var right_diff = center_height - right_height;
                var left_diff = center_height - left_height;

                $(".vslogo_block").height((vslogo_height + right_diff) + "px");
                $(".perf_block").height((perf_height + left_diff) + "px");


                //$("title").text("" + center_height + "  " + right_height + "  " + vslogo_height);

            }

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
