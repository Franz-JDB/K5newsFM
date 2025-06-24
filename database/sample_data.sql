-- Sample data for K5News Database
USE k5news_db;

-- Insert sample articles
INSERT INTO articles (headline, slug, content, excerpt, featured_image, category_id, author_id, status, is_featured, is_breaking_news, published_at) VALUES
('Breaking: Major Economic Policy Changes Announced', 'breaking-major-economic-policy-changes-announced', 
'<p>The government today announced sweeping changes to the national economic policy that will affect millions of citizens across the country. The new measures include tax reforms, infrastructure investments, and support for small businesses.</p>

<p>According to the official statement released this morning, the policy changes aim to stimulate economic growth while ensuring sustainable development for the next decade. Key highlights include:</p>

<ul>
<li>Reduction in corporate tax rates for small and medium enterprises</li>
<li>Increased funding for infrastructure projects</li>
<li>New incentives for renewable energy investments</li>
<li>Enhanced support for local manufacturing</li>
</ul>

<p>Economic experts have welcomed the announcement, calling it a "bold step forward" that could significantly boost the country''s economic recovery efforts.</p>

<p>"These changes demonstrate a clear vision for economic transformation," said Dr. Maria Santos, a leading economist at the National Economic Institute. "The focus on sustainable growth and local business support is particularly encouraging."</p>

<p>The implementation of these policies is expected to begin within the next three months, with full rollout completed by the end of the fiscal year.</p>',
'The government announces comprehensive economic policy reforms including tax changes, infrastructure funding, and business support measures.',
'https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=800',
2, 2, 'published', 1, 1, NOW()),

('Technology Sector Sees Record Growth in Q3', 'technology-sector-sees-record-growth-q3',
'<p>The technology sector has reported unprecedented growth in the third quarter of this year, with major companies posting record-breaking profits and expanding their workforce significantly.</p>

<p>According to the latest industry report, the sector grew by 15.3% compared to the previous quarter, marking the highest growth rate in the past five years. This surge has been attributed to several factors:</p>

<ul>
<li>Increased demand for digital services during the pandemic</li>
<li>Rapid adoption of cloud computing solutions</li>
<li>Growth in e-commerce and online platforms</li>
<li>Investment in artificial intelligence and machine learning</li>
</ul>

<p>Leading tech companies have announced plans to hire thousands of new employees, with many offering competitive salaries and remote work options to attract top talent.</p>

<p>"The technology sector is driving innovation and creating opportunities across all industries," said David Chen, Technology Analyst at TechInsights. "This growth trend is expected to continue well into next year."</p>

<p>Small and medium-sized tech startups have also benefited from this growth, with many receiving significant funding from venture capital firms and angel investors.</p>',
'Technology companies report 15.3% growth in Q3, with record profits and massive hiring plans across the sector.',
'https://images.unsplash.com/photo-1518709268805-4e9042af2176?w=800',
4, 4, 'published', 1, 0, NOW()),

('Local Sports Team Wins Championship Title', 'local-sports-team-wins-championship-title',
'<p>In an electrifying match that went down to the wire, our local sports team has secured the championship title for the first time in over a decade. The victory came after a nail-biting final that had fans on the edge of their seats.</p>

<p>The team, led by captain Michael Rodriguez, displayed exceptional skill and determination throughout the season, finishing with an impressive record of 28 wins and only 4 losses. The championship game was a true testament to their hard work and dedication.</p>

<p>"This victory means everything to our team and our community," said Rodriguez during the post-game celebration. "We''ve worked tirelessly for this moment, and to finally achieve it is absolutely incredible."</p>

<p>Key highlights from the championship game include:</p>

<ul>
<li>Outstanding defensive performance in the final quarter</li>
<li>Clutch scoring from rookie player Sarah Johnson</li>
<li>Strategic coaching decisions that turned the tide</li>
<li>Unwavering support from the home crowd</li>
</ul>

<p>The victory parade is scheduled for next weekend, with thousands of fans expected to line the streets to celebrate this historic achievement.</p>

<p>Coach Williams praised his team''s resilience: "These players never gave up, even when the odds were against them. This championship is a result of their belief in each other and their commitment to excellence."</p>',
'Local team secures championship victory in thrilling final match, marking their first title in over a decade.',
'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=800',
5, 1, 'published', 0, 0, NOW()),

('New Health Guidelines Released for Public Safety', 'new-health-guidelines-released-public-safety',
'<p>The Department of Health has released updated guidelines for public safety and wellness, incorporating the latest research findings and expert recommendations. These new guidelines aim to improve community health outcomes and prevent the spread of infectious diseases.</p>

<p>The comprehensive document covers various aspects of public health, including:</p>

<ul>
<li>Updated vaccination recommendations for all age groups</li>
<li>Enhanced protocols for public spaces and gatherings</li>
<li>Mental health support and resources</li>
<li>Nutrition and exercise guidelines</li>
<li>Preventive care recommendations</li>
</ul>

<p>Dr. Emily Thompson, Director of Public Health, emphasized the importance of these guidelines: "Our goal is to provide clear, evidence-based recommendations that everyone can follow to protect themselves and their communities."</p>

<p>The guidelines also include specific recommendations for vulnerable populations, including elderly individuals, children, and those with pre-existing health conditions.</p>

<p>Local healthcare providers have been briefed on the new guidelines and are ready to implement them in their practices. Public awareness campaigns will begin next month to ensure widespread adoption of these health measures.</p>

<p>"These guidelines represent our commitment to community health and safety," said Health Minister Dr. Robert Chen. "We encourage everyone to review and follow these recommendations for the benefit of our entire community."</p>',
'Department of Health releases comprehensive new guidelines for public safety and wellness, incorporating latest research and expert recommendations.',
'https://images.unsplash.com/photo-1576091160399-112ba8d25d1f?w=800',
7, 3, 'published', 0, 0, NOW()),

('Education Reform Bill Passes in Parliament', 'education-reform-bill-passes-parliament',
'<p>In a landmark decision, Parliament has passed the comprehensive Education Reform Bill, which will bring significant changes to the country''s education system. The bill received overwhelming support from both government and opposition members.</p>

<p>The reform package includes several key initiatives designed to improve educational outcomes and accessibility:</p>

<ul>
<li>Increased funding for public schools and universities</li>
<li>Implementation of new curriculum standards</li>
<li>Enhanced teacher training and development programs</li>
<li>Expansion of digital learning resources</li>
<li>Improved support for students with special needs</li>
</ul>

<p>Education Minister Sarah Williams hailed the passage as "a historic moment for our education system." She emphasized that the reforms will ensure every child has access to quality education regardless of their background or location.</p>

<p>The bill also includes provisions for:</p>

<ul>
<li>Modernization of school infrastructure</li>
<li>Integration of technology in classrooms</li>
<li>Partnerships with private sector for skill development</li>
<li>Enhanced vocational training programs</li>
</ul>

<p>Implementation of the reforms will begin in the next academic year, with a phased approach to ensure smooth transition. The government has allocated substantial funding to support these initiatives.</p>

<p>"This reform represents our commitment to building a stronger, more inclusive education system," said Prime Minister Johnson during the parliamentary session. "Education is the foundation of our nation''s future."</p>',
'Parliament passes comprehensive Education Reform Bill with overwhelming support, bringing significant changes to the education system.',
'https://images.unsplash.com/photo-1523050854058-8df90110c9e1?w=800',
8, 2, 'published', 1, 0, NOW());

-- Insert sample comments
INSERT INTO comments (article_id, name, email, comment, is_approved) VALUES
(1, 'John Smith', 'john.smith@email.com', 'This is great news! The economic reforms will definitely help small businesses like mine.', 1),
(1, 'Maria Garcia', 'maria.garcia@email.com', 'I hope these changes will create more job opportunities in our area.', 1),
(2, 'Tech Enthusiast', 'tech@email.com', 'The growth in the tech sector is amazing! This is creating so many opportunities for young professionals.', 1),
(3, 'Sports Fan', 'fan@email.com', 'What an incredible game! The team really deserved this victory after such a great season.', 1),
(4, 'Health Worker', 'health@email.com', 'These guidelines are much needed. Thank you for keeping our community safe.', 1),
(5, 'Teacher', 'teacher@email.com', 'As an educator, I''m excited about these reforms. They will make a real difference in our classrooms.', 1);

-- Insert sample tags
INSERT INTO tags (name, slug) VALUES
('Economy', 'economy'),
('Technology', 'technology'),
('Sports', 'sports'),
('Health', 'health'),
('Education', 'education'),
('Policy', 'policy'),
('Reform', 'reform'),
('Championship', 'championship'),
('Growth', 'growth'),
('Safety', 'safety');

-- Link tags to articles
INSERT INTO article_tags (article_id, tag_id) VALUES
(1, 1), (1, 6), (1, 7), -- Economy, Policy, Reform
(2, 2), (2, 9), -- Technology, Growth
(3, 3), (3, 8), -- Sports, Championship
(4, 4), (4, 10), -- Health, Safety
(5, 5), (5, 6), (5, 7); -- Education, Policy, Reform 