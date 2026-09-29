/**
 * BlogHub — Centralized Dummy Data Layer (data.js)
 * High-fidelity, consistent mock dataset mirroring Phase 2 API responses.
 *
 * Contents:
 * - 12 Categories (article count exactly matches articles array)
 * - 10 Authors (article count exactly matches authored articles, 2+ per author)
 * - 24 Articles (rich HTML body, varied reading times, categories, authors)
 * - 55 Comments (spread across 11 articles, counts strictly matched)
 * - 10 Testimonials (8 positive 4-5 stars, 2 balanced 3 stars)
 */

const categories = [
  {
    id: 1,
    name: 'Technology',
    slug: 'technology',
    icon: 'fa-solid fa-microchip',
    description: 'Breakthroughs in artificial intelligence, cloud architectures, and modern software development.',
    articleCount: 2
  },
  {
    id: 2,
    name: 'Education',
    slug: 'education',
    icon: 'fa-solid fa-graduation-cap',
    description: 'Transformative pedagogy, lifelong learning strategies, and the digital classroom evolution.',
    articleCount: 2
  },
  {
    id: 3,
    name: 'Travel',
    slug: 'travel',
    icon: 'fa-solid fa-compass',
    description: 'Slow travel itineraries, hidden remote destinations, and mindful cultural expeditions.',
    articleCount: 2
  },
  {
    id: 4,
    name: 'Food',
    slug: 'food',
    icon: 'fa-solid fa-utensils',
    description: 'Culinary traditions, farm-to-table sustainability, and artisanal gastronomy across the globe.',
    articleCount: 2
  },
  {
    id: 5,
    name: 'Business',
    slug: 'business',
    icon: 'fa-solid fa-chart-line',
    description: 'Strategic leadership, venture dynamics, and resilient operational execution in modern markets.',
    articleCount: 2
  },
  {
    id: 6,
    name: 'Sports',
    slug: 'sports',
    icon: 'fa-solid fa-medal',
    description: 'Athletic biomechanics, tactical sports analytics, and legendary endurance journeys.',
    articleCount: 2
  },
  {
    id: 7,
    name: 'Health',
    slug: 'health',
    icon: 'fa-solid fa-heart-pulse',
    description: 'Evidence-based wellness, preventative neuroscience, and holistic mental and physical health.',
    articleCount: 2
  },
  {
    id: 8,
    name: 'Lifestyle',
    slug: 'lifestyle',
    icon: 'fa-solid fa-couch',
    description: 'Intentional living, workspace architecture, mindfulness practices, and daily creative rituals.',
    articleCount: 2
  },
  {
    id: 9,
    name: 'Finance',
    slug: 'finance',
    icon: 'fa-solid fa-wallet',
    description: 'Macroeconomic trends, ethical investing, personal wealth structuring, and decentralized protocols.',
    articleCount: 2
  },
  {
    id: 10,
    name: 'Science',
    slug: 'science',
    icon: 'fa-solid fa-atom',
    description: 'Astrophysics, clean energy transitions, synthetic biology, and empirical frontiers.',
    articleCount: 2
  },
  {
    id: 11,
    name: 'Art',
    slug: 'art',
    icon: 'fa-solid fa-palette',
    description: 'Visual culture, gallery retrospectives, generative aesthetics, and contemporary creative movements.',
    articleCount: 2
  },
  {
    id: 12,
    name: 'Culture',
    slug: 'culture',
    icon: 'fa-solid fa-landmark',
    description: 'Literary critiques, sociology of digital spaces, and philosophical inquiries into modern life.',
    articleCount: 2
  }
];

const authors = [
  {
    id: 1,
    name: 'Elena Rostova',
    slug: 'elena-rostova',
    photo: 'https://i.pravatar.cc/300?img=47',
    coverImage: 'https://picsum.photos/seed/elena-cover/1200/400',
    tagline: 'Principal Distributed Systems Engineer & Tech Essayist',
    bio: 'Elena has spent 14 years building fault-tolerant infrastructure and researching the social impact of scalable computing. Based in Berlin, she writes regularly about software craft, systems thinking, and engineering culture.',
    specialty: 'Distributed Systems & Tech',
    social: {
      twitter: 'https://twitter.com',
      github: 'https://github.com',
      linkedin: 'https://linkedin.com'
    },
    followers: 1420,
    following: 184,
    articleCount: 3 // Articles 1, 11, 21
  },
  {
    id: 2,
    name: 'Marcus Chen',
    slug: 'marcus-chen',
    photo: 'https://i.pravatar.cc/300?img=12',
    coverImage: 'https://picsum.photos/seed/marcus-cover/1200/400',
    tagline: 'Design Director & Editorial Typographer',
    bio: 'Marcus oversees design systems and brand identity for digital publications. He champions accessibility, rigorous typography scales, and human-first user experiences.',
    specialty: 'UI/UX & Editorial Design',
    social: {
      twitter: 'https://twitter.com',
      github: 'https://github.com',
      linkedin: 'https://linkedin.com'
    },
    followers: 2150,
    following: 310,
    articleCount: 3 // Articles 2, 12, 22
  },
  {
    id: 3,
    name: 'Dr. Sarah Jenkins',
    slug: 'sarah-jenkins',
    photo: 'https://i.pravatar.cc/300?img=32',
    coverImage: 'https://picsum.photos/seed/sarah-cover/1200/400',
    tagline: 'Cognitive Neuroscientist & Pedagogy Researcher',
    bio: 'Dr. Jenkins investigates the intersection of neuroplasticity, memory retention, and digital education interfaces at Oxford. Author of "The Focused Mind in a Fractured Age".',
    specialty: 'Cognitive Science & Education',
    social: {
      twitter: 'https://twitter.com',
      linkedin: 'https://linkedin.com'
    },
    followers: 3890,
    following: 142,
    articleCount: 3 // Articles 3, 13, 23
  },
  {
    id: 4,
    name: 'Mateo Morales',
    slug: 'mateo-morales',
    photo: 'https://i.pravatar.cc/300?img=60',
    coverImage: 'https://picsum.photos/seed/mateo-cover/1200/400',
    tagline: 'Documentary Photojournalist & Travel Essayist',
    bio: 'Mateo travels the Mediterranean and Andean corridors documenting slow living, indigenous crafts, and vanishing mountain trails. His essays emphasize respect for local ecologies.',
    specialty: 'Travel & Documentary',
    social: {
      twitter: 'https://twitter.com',
      linkedin: 'https://linkedin.com'
    },
    followers: 1820,
    following: 245,
    articleCount: 3 // Articles 4, 14, 24
  },
  {
    id: 5,
    name: 'Chef Camille Laurent',
    slug: 'camille-laurent',
    photo: 'https://i.pravatar.cc/300?img=49',
    coverImage: 'https://picsum.photos/seed/camille-cover/1200/400',
    tagline: 'Gastronomy Chronicler & Sustainable Food Advocate',
    bio: 'Former pastry chef turned food systems researcher. Camille investigates heritage fermentation, regenerative farming, and the cultural history of bread.',
    specialty: 'Culinary Traditions & Gastronomy',
    social: {
      twitter: 'https://twitter.com',
      linkedin: 'https://linkedin.com'
    },
    followers: 2980,
    following: 420,
    articleCount: 2 // Articles 5, 15
  },
  {
    id: 6,
    name: 'Julian Vance',
    slug: 'julian-vance',
    photo: 'https://i.pravatar.cc/300?img=53',
    coverImage: 'https://picsum.photos/seed/julian-cover/1200/400',
    tagline: 'Venture Strategist & Organizational Designer',
    bio: 'Julian advises climate tech and healthcare startups on scaling sustainable operations, ethical governance, and long-term equity planning.',
    specialty: 'Business Strategy & Scaling',
    social: {
      twitter: 'https://twitter.com',
      github: 'https://github.com',
      linkedin: 'https://linkedin.com'
    },
    followers: 4120,
    following: 195,
    articleCount: 2 // Articles 6, 16
  },
  {
    id: 7,
    name: 'Amina Nour',
    slug: 'amina-nour',
    photo: 'https://i.pravatar.cc/300?img=26',
    coverImage: 'https://picsum.photos/seed/amina-cover/1200/400',
    tagline: 'Endurance Athlete & Performance Physiologist',
    bio: 'Ultrarunner and sports science researcher focusing on aerobic endurance, heart rate variability, and psychological resilience under extreme conditions.',
    specialty: 'Sports Physiology & Performance',
    social: {
      twitter: 'https://twitter.com',
      linkedin: 'https://linkedin.com'
    },
    followers: 1670,
    following: 153,
    articleCount: 2 // Articles 7, 17
  },
  {
    id: 8,
    name: 'Dr. Aris Thorne',
    slug: 'aris-thorne',
    photo: 'https://i.pravatar.cc/300?img=15',
    coverImage: 'https://picsum.photos/seed/aris-cover/1200/400',
    tagline: 'Preventative Medicine Physician & Longevity Author',
    bio: 'Dr. Thorne translates complex metabolic research and circadian biology into actionable daily protocols for optimal healthspan and cognitive vitality.',
    specialty: 'Preventative Medicine & Healthspan',
    social: {
      twitter: 'https://twitter.com',
      linkedin: 'https://linkedin.com'
    },
    followers: 5240,
    following: 210,
    articleCount: 2 // Articles 8, 18
  },
  {
    id: 9,
    name: 'Priya Sharma',
    slug: 'priya-sharma',
    photo: 'https://i.pravatar.cc/300?img=44',
    coverImage: 'https://picsum.photos/seed/priya-cover/1200/400',
    tagline: 'Quantitative Analyst & Macroeconomics Columnist',
    bio: 'Specialist in central bank policies, sovereign debt cycles, and ethical venture finance. Priya contributes regular macro briefs and institutional models.',
    specialty: 'Macroeconomics & Capital Markets',
    social: {
      twitter: 'https://twitter.com',
      linkedin: 'https://linkedin.com'
    },
    followers: 3410,
    following: 178,
    articleCount: 2 // Articles 9, 19
  },
  {
    id: 10,
    name: 'David Kim',
    slug: 'david-kim',
    photo: 'https://i.pravatar.cc/300?img=68',
    coverImage: 'https://picsum.photos/seed/david-cover/1200/400',
    tagline: 'Curator, Art Critic & Architectural Theorist',
    bio: 'David writes about the tension between handcraft and algorithm in contemporary architecture, industrial design, and public civic monuments.',
    specialty: 'Visual Arts & Architecture',
    social: {
      twitter: 'https://twitter.com',
      linkedin: 'https://linkedin.com'
    },
    followers: 2680,
    following: 340,
    articleCount: 2 // Articles 10, 20
  }
];

const articles = [
  // 1. Technology (Author 1: Elena)
  {
    id: 1,
    slug: 'the-architectural-elegance-of-component-first-styling',
    title: 'The Architectural Elegance of Component-First Styling',
    excerpt: 'Exploring how strict design tokens, semantic markup, and modular CSS establish resilient user interfaces without bloated build pipelines.',
    category: 'Technology',
    categorySlug: 'technology',
    tags: ['CSS3', 'Design Systems', 'Architecture', 'Frontend'],
    authorId: 1,
    date: 'Sep 24, 2026',
    readingTime: '6 min read',
    views: 3420,
    likes: 184,
    commentCount: 7,
    image: 'https://picsum.photos/seed/tech-arch-1/1000/600',
    status: 'published',
    content: `
      <h2>The Re-Emergence of Direct Fundamentals</h2>
      <p>Over the last decade, frontend engineering underwent an explosion of compilation layers, meta-frameworks, and preprocessors. While these tooling advancements brought immense power, they also introduced maintenance fragility, brittle node dependency trees, and high abstraction overhead. Returning to pure CSS variables, semantic HTML5 tags, and standard browser primitives allows engineering teams to ship faster with unmatched clarity.</p>

      <blockquote>
        "True engineering maturity is not demonstrated by how many abstraction layers you can stack, but by how elegantly you can fulfill user expectations with minimal moving parts."
      </blockquote>

      <h3>Design Tokens as Single Source of Truth</h3>
      <p>When design tokens are defined natively inside <code>:root</code>, theming transitions from an arduous rebuild cycle into a simple attribute mutation. Modern browsers handle custom property cascading natively with zero runtime JavaScript penalty:</p>

      <pre><code class="language-css">:root {
  --color-primary: #1F2A44;
  --color-accent: #C8461F;
  --space-4: 16px;
  --btn-height: 44px;
}

[data-theme="dark"] {
  --color-primary: #9BB7E7;
  --color-bg: #0D1117;
}</code></pre>

      <p>By strictly disciplining typography scales and spacing units to multiples of eight, interfaces naturally achieve visual cadence and effortless responsiveness across every viewport breakpoint.</p>
    `
  },

  // 2. Education (Author 2: Marcus)
  {
    id: 2,
    slug: 'digital-pedagogy-and-the-decay-of-linear-textbooks',
    title: 'Digital Pedagogy and the Decay of Linear Textbooks',
    excerpt: 'How interactive reading environments, spaced-repetition modules, and responsive typography are transforming modern student comprehension.',
    category: 'Education',
    categorySlug: 'education',
    tags: ['Pedagogy', 'Learning', 'EdTech', 'Typography'],
    authorId: 2,
    date: 'Sep 22, 2026',
    readingTime: '7 min read',
    views: 2890,
    likes: 142,
    commentCount: 6,
    image: 'https://picsum.photos/seed/edu-textbook-2/1000/600',
    content: `
      <h2>Beyond the Static Printed Page</h2>
      <p>For centuries, the academic textbook enforced a strictly linear model of knowledge acquisition. Chapters followed chronological or thematic hierarchies that assumed all learners progressed at identical tempos. In the contemporary digital classroom, this rigid paradigm is collapsing in favor of non-linear conceptual graphs.</p>
      
      <h3>The Cognitive Role of Editorial Design</h3>
      <p>Reading complex academic literature on screens introduces severe eye fatigue when typography is neglected. Establishing high-contrast serif headlines with generous line heights (1.6) and comfortable line measures (65–75 characters) dramatically reduces cognitive strain during multi-hour study sessions.</p>

      <blockquote>
        "When typography serves comprehension rather than decoration, students retain nuanced arguments with thirty percent greater fidelity."
      </blockquote>

      <p>As educational institutions embrace web-native content management platforms, the priority must center on accessibility standards (WCAG AA compliance) and lightning-fast load times on entry-level mobile devices.</p>
    `
  },

  // 3. Travel (Author 3: Dr. Sarah)
  {
    id: 3,
    slug: 'the-art-of-slow-travel-along-the-dalmatian-coast',
    title: 'The Art of Slow Travel Along the Dalmatian Coast',
    excerpt: 'Trading crowded cruise ports for ancient stone villages, secluded olive groves, and the rhythmic quietude of the Adriatic archipelago.',
    category: 'Travel',
    categorySlug: 'travel',
    tags: ['Travel', 'Adriatic', 'Slow Living', 'Culture'],
    authorId: 3,
    date: 'Sep 20, 2026',
    readingTime: '5 min read',
    views: 4120,
    likes: 230,
    commentCount: 5,
    image: 'https://picsum.photos/seed/travel-dalmatia-3/1000/600',
    content: `
      <h2>Unrushed Horizons</h2>
      <p>Modern transit has compressed geography, allowing travelers to hop across capitals in mere hours. Yet with this acceleration, something essential has vanished: the texture of the journey itself. Along the winding coastal roads of Dalmatia, time recalibrates to the gentle cadence of lapping sea waters and cicadas in ancient pines.</p>

      <h3>Living Within Stone Architecture</h3>
      <p>In villages like Pučišća on the island of Brač, limestone quarries have supplied stone for Roman palaces and modern cathedrals alike. Walking through narrow cobblestone alleyways at dawn reveals a community that honors local agriculture, seasonal olive pressing, and communal evening dinners that stretch into midnight.</p>

      <blockquote>
        "To travel slowly is not merely to spend more days in a location; it is to surrender your schedule to the rhythm of the local inhabitants."
      </blockquote>

      <p>By disembarking from high-speed ferries and exploring on foot or wooden sailing dinghies, travelers cultivate a relationship with coastal ecology that tourist itineraries can never replicate.</p>
    `
  },

  // 4. Food (Author 4: Mateo)
  {
    id: 4,
    slug: 'ancient-grains-and-the-quiet-revival-of-sourdough',
    title: 'Ancient Grains and the Quiet Revival of Sourdough',
    excerpt: 'Investigating heritage Einkorn, Emmer, and long-fermentation biology that is restoring nutritional integrity to artisanal hearth breads.',
    category: 'Food',
    categorySlug: 'food',
    tags: ['Food', 'Sourdough', 'Fermentation', 'Baking'],
    authorId: 4,
    date: 'Sep 18, 2026',
    readingTime: '8 min read',
    views: 3180,
    likes: 198,
    commentCount: 6,
    image: 'https://picsum.photos/seed/food-sourdough-4/1000/600',
    content: `
      <h2>The Industrial Flattening of Flour</h2>
      <p>During the mid-twentieth century, roller-milling technology separated wheat grain into isolated endosperm, striping away the nutrient-dense germ and bran to yield white flour with indefinite shelf stability. This convenience came at catastrophic expense to flavor complexity and gut digestibility.</p>

      <h3>The Microbiology of Wild Fermentation</h3>
      <p>True sourdough fermentation relies on a symbiotic colony of wild yeasts and lactic acid bacteria (principally <em>Lactobacillus sanfranciscensis</em>). Over a 24 to 36-hour slow fermentation cycle, these microorganisms break down phytic acid and pre-digest gluten proteins:</p>

      <blockquote>
        "Flour, spring water, and sea salt. When animated by wild microflora and patience, three humble elements become sustenance of extraordinary complexity."
      </blockquote>

      <p>Bakeries reintroducing heirloom heritage grains like Einkorn and Red Fife are demonstrating that artisanal technique is not a nostalgic indulgence, but a critical imperative for sustainable human nutrition.</p>
    `
  },

  // 5. Business (Author 5: Camille)
  {
    id: 5,
    slug: 'sustainable-unit-economics-in-the-post-growth-era',
    title: 'Sustainable Unit Economics in the Post-Growth Era',
    excerpt: 'Why hyper-growth venture subsidies have yielded to disciplined cash-flow positive business models with durable customer retention.',
    category: 'Business',
    categorySlug: 'business',
    tags: ['Business', 'Economics', 'Venture Capital', 'Startups'],
    authorId: 5,
    date: 'Sep 16, 2026',
    readingTime: '9 min read',
    views: 4720,
    likes: 312,
    commentCount: 5,
    image: 'https://picsum.photos/seed/biz-growth-5/1000/600',
    content: `
      <h2>The Hangover of Free Capital</h2>
      <p>For more than a decade, zero-interest-rate monetary policies created an environment where tech startups subsidized user acquisition at steep losses. Growth at all costs became the sole operational metric, papering over deeply broken unit economics and high customer churn.</p>

      <h3>Return to First-Principles Unit Margin</h3>
      <p>As capital costs normalized, executive leadership faced an overdue reckoning. The organizations currently prospering are those that maintained tight gross margin discipline, resisted premature scaling, and focused relentlessly on organic customer retention rather than paid acquisition funnel burn.</p>

      <blockquote>
        "A business that requires external cash infusions merely to service existing customer volume is not a software company; it is an unsustainable charity."
      </blockquote>

      <p>Sustainable operating margins, transparent pricing tiers, and lean engineering teams represent the new gold standard for venture investment moving through the late 2020s.</p>
    `
  },

  // 6. Sports (Author 6: Julian)
  {
    id: 6,
    slug: 'biomechanics-and-the-quest-for-the-sub-two-hour-marathon',
    title: 'Biomechanics and the Quest for the Sub-Two-Hour Marathon',
    excerpt: 'Analyzing carbon-fiber plate dynamics, energy return elastomers, and aerodynamic drafting tactics reshaping competitive distance athletics.',
    category: 'Sports',
    categorySlug: 'sports',
    tags: ['Athletics', 'Running', 'Biomechanics', 'Endurance'],
    authorId: 6,
    date: 'Sep 14, 2026',
    readingTime: '6 min read',
    views: 2950,
    likes: 165,
    commentCount: 4,
    image: 'https://picsum.photos/seed/sports-marathon-6/1000/600',
    content: `
      <h2>The Physics of Human Locomotion</h2>
      <p>Running is fundamentally a cyclical conservation of kinetic energy. With every foot strike, muscles, tendons, and ligaments act as biological springs, absorbing gravitational impact and rebounding the athlete forward into the next stride phase.</p>

      <h3>The Super-Shoe Paradigm Shift</h3>
      <p>The introduction of curved carbon-composite plates embedded within highly resilient polyether block amide (PEBA) foams reduced the energetic cost of running by over 4%. This equipment breakthrough rewrote world record tables across all road distances:</p>

      <blockquote>
        "Records that stood unchallenged for generations fell not because human biology mutated overnight, but because thermodynamic efficiency met precision materials science."
      </blockquote>

      <p>Combined with real-time continuous lactate monitoring and tailored carbohydrate hydration strategies, human endurance limits continue to be pushed into previously unimaginable territory.</p>
    `
  },

  // 7. Health (Author 7: Amina)
  {
    id: 7,
    slug: 'circadian-biology-and-the-metabolic-clock',
    title: 'Circadian Biology and the Metabolic Clock',
    excerpt: 'How light exposure, meal timing windows, and sleep architecture synchronize cellular repair mechanisms across vital organ systems.',
    category: 'Health',
    categorySlug: 'health',
    tags: ['Health', 'Circadian', 'Sleep', 'Longevity'],
    authorId: 7,
    date: 'Sep 12, 2026',
    readingTime: '7 min read',
    views: 3880,
    likes: 245,
    commentCount: 5,
    image: 'https://picsum.photos/seed/health-circadian-7/1000/600',
    content: `
      <h2>The Molecular Clock in Every Cell</h2>
      <p>Every organ in the human body operates on an autonomous 24-hour molecular rhythm regulated by the suprachiasmatic nucleus (SCN) in the hypothalamus. While light remains the master zeitgeber, nutrient ingestion acts as an equally potent peripheral synchronizer for the liver, pancreas, and gastrointestinal tract.</p>

      <h3>Practical Guidelines for Circadian Entrainment</h3>
      <p>Disruptions caused by nocturnal blue light and erratic eating windows correlate directly with insulin resistance, systemic neuroinflammation, and impaired deep slow-wave sleep phases.</p>

      <blockquote>
        "Syncing your feeding window with daylight is one of the most cost-effective and empirically validated interventions for sustained metabolic vigor."
      </blockquote>

      <p>Prioritizing morning natural sunlight within thirty minutes of waking and concluding caloric intake at least three hours before bed aligns cellular autophagic pathways for nightly repair.</p>
    `
  },

  // 8. Lifestyle (Author 8: Dr. Aris)
  {
    id: 8,
    slug: 'workspace-architecture-for-deep-cognitive-work',
    title: 'Workspace Architecture for Deep Cognitive Work',
    excerpt: 'Designing physical and digital environments that eliminate distraction, cultivate quiet focus, and sustain daily creative output.',
    category: 'Lifestyle',
    categorySlug: 'lifestyle',
    tags: ['Productivity', 'Workspace', 'Mindfulness', 'Design'],
    authorId: 8,
    date: 'Sep 10, 2026',
    readingTime: '5 min read',
    views: 2640,
    likes: 178,
    commentCount: 4,
    image: 'https://picsum.photos/seed/life-desk-8/1000/600',
    content: `
      <h2>The Geography of Attention</h2>
      <p>Attention is an exceptionally fragile cognitive resource. Every open browser tab, notification badge, and cluttered desktop surface exerts a subtle micro-tax on executive working memory, pulling the brain out of deep associative thinking into reactive triage.</p>

      <h3>Physical Minimalism Meets Digital Hygiene</h3>
      <p>Crafting an intentional workspace requires both acoustic and visual discipline. Natural cross-ventilation, indirect warm task lighting (2700K), tactile notebooks, and hardware disconnected from push notifications create a sanctuary for sustained intellectual effort.</p>

      <blockquote>
        "Your environment is your silent collaborator. If your desk is disordered and frantic, your prose and code will inevitably bear the same hallmarks."
      </blockquote>

      <p>By protecting four uninterrupted morning hours for prime creative output, writers and engineers achieve in one morning what fragmented schedules struggle to produce across an entire work week.</p>
    `
  },

  // 9. Finance (Author 9: Priya)
  {
    id: 9,
    slug: 'decentralized-clearing-and-the-future-of-cross-border-settlement',
    title: 'Decentralized Clearing and the Future of Cross-Border Settlement',
    excerpt: 'Examining how real-time gross settlement architectures and tokenized collateral are modernizing legacy correspondent banking systems.',
    category: 'Finance',
    categorySlug: 'finance',
    tags: ['Finance', 'Fintech', 'Banking', 'Macroeconomics'],
    authorId: 9,
    date: 'Sep 08, 2026',
    readingTime: '8 min read',
    views: 3940,
    likes: 215,
    commentCount: 5,
    image: 'https://picsum.photos/seed/finance-fintech-9/1000/600',
    content: `
      <h2>Frictions in Legacy Correspondent Rails</h2>
      <p>International commerce still relies heavily on correspondent banking relationships governed by messaging protocols conceived in the 1970s. Payments traversing multiple jurisdictions often endure two to four days of transit latency, counterparty risks, and multi-tier fee deductions.</p>

      <h3>Atomic Settlement via Programmable Ledgers</h3>
      <p>The convergence of central bank digital currency pilots and tokenized liquidity pools enables atomic Delivery-versus-Payment (DvP) transactions. Value and ownership certificates change hands simultaneously, eliminating settlement risk entirely:</p>

      <blockquote>
        "The elimination of settlement delay unlocks trillions of dollars in trapped nostro/vostro buffer liquidity worldwide."
      </blockquote>

      <p>Institutional treasuries are rapidly adopting these automated settlement fabrics to achieve continuous balance sheet optimization and eliminate foreign exchange slippage.</p>
    `
  },

  // 10. Science (Author 10: David)
  {
    id: 10,
    slug: 'the-james-webb-observatory-and-primordial-galaxy-formation',
    title: 'The James Webb Observatory and Primordial Galaxy Formation',
    excerpt: 'Surprising infrared cosmological discoveries that are challenging standard Lambda-CDM models of the early universe.',
    category: 'Science',
    categorySlug: 'science',
    tags: ['Cosmology', 'Astronomy', 'Astrophysics', 'Space'],
    authorId: 10,
    date: 'Sep 06, 2026',
    readingTime: '9 min read',
    views: 5210,
    likes: 388,
    commentCount: 4,
    image: 'https://picsum.photos/seed/science-jwst-10/1000/600',
    content: `
      <h2>Peering into the Cosmic Dawn</h2>
      <p>When the James Webb Space Telescope opened its 6.5-meter gold-coated beryllium mirror to the deep cosmos, astronomers anticipated confirming gradual hierarchical galaxy assembly. Instead, deep-field spectroscopic surveys revealed massive, luminous galaxies already thriving just 300 million years following the Big Bang.</p>

      <h3>The "Impossible Early Galaxy" Conundrum</h3>
      <p>Standard cosmological simulations predicted that gas clouds would take hundreds of millions of additional years to cool, collapse, and ignite multiple generations of stellar nurseries:</p>

      <blockquote>
        "The universe was creating giant star clusters with ferocious efficiency far earlier than theoretical astrophysics ever anticipated."
      </blockquote>

      <p>These findings have stimulated revisions to primordial black hole seed hypotheses and dark matter clustering models, ushering in a golden age of empirical astrophysics.</p>
    `
  },

  // 11. Art (Author 1: Elena)
  {
    id: 11,
    slug: 'analog-warmth-in-a-synthetic-visual-landscape',
    title: 'Analog Warmth in a Synthetic Visual Landscape',
    excerpt: 'Why painters, printmakers, and ceramicists are rediscovering tactile handcraft as an antidote to algorithmic visual homogeneity.',
    category: 'Art',
    categorySlug: 'art',
    tags: ['Art', 'Craft', 'Printmaking', 'Culture'],
    authorId: 1,
    date: 'Sep 04, 2026',
    readingTime: '5 min read',
    views: 2410,
    likes: 153,
    commentCount: 4,
    image: 'https://picsum.photos/seed/art-analog-11/1000/600',
    content: `
      <h2>The Fatigue of Digital Perfection</h2>
      <p>When synthetic image generation tools democratized hyper-detailed digital renderings at the click of a prompt, a curious counter-reaction ignited across contemporary gallery spaces. Flawless gradient meshes and mathematically perfect symmetries began to feel curiously sterile.</p>

      <h3>The Irreplaceable Texture of Handcraft</h3>
      <p>Artists are turning back to copperplate etching, woodcut relief printing, and wheel-thrown stoneware ceramics. In these mediums, the physical resistance of raw pigment, paper grain, and kiln heat introduces serendipitous imperfections that no algorithm can premeditate:</p>

      <blockquote>
        "True beauty frequently resides inside the tremor of the artist's hand—the intentional vulnerability of an irreproducible human mark."
      </blockquote>

      <p>Collectors and audiences increasingly seek art objects that bear the tactile evidence of physical presence, patience, and unhurried labor.</p>
    `
  },

  // 12. Culture (Author 2: Marcus)
  {
    id: 12,
    slug: 'the-demise-of-the-infinite-scroll-and-the-rise-of-editorial-curation',
    title: 'The Demise of the Infinite Scroll and the Rise of Editorial Curation',
    excerpt: 'Reflecting on reader fatigue with recommendation algorithms and the resurgent appetite for intentional, finished digital publications.',
    category: 'Culture',
    categorySlug: 'culture',
    tags: ['Culture', 'Publishing', 'Internet', 'Media'],
    authorId: 2,
    date: 'Sep 02, 2026',
    readingTime: '6 min read',
    views: 3670,
    likes: 276,
    commentCount: 0,
    image: 'https://picsum.photos/seed/culture-scroll-12/1000/600',
    content: `
      <h2>The Bottomless Pit of Engagement Algorithms</h2>
      <p>For fifteen years, consumer software design celebrated the infinite scroll as the pinnacle of interface frictionless delight. Yet stripping away pagination, stopping cues, and clear conclusions transformed reading from an intentional act of contemplation into a compulsive dopamine loop.</p>

      <h3>The Dignity of a Finished Edition</h3>
      <p>Modern readers are experiencing feed fatigue. In its place, independent editorial platforms that celebrate curated digests, numbered essays, and explicit "end of article" landmarks are enjoying unprecedented subscriber loyalty:</p>

      <blockquote>
        "There is profound cognitive peace in turning the final page of a magazine and knowing that you have finished something thoughtfully conceived."
      </blockquote>

      <p>By returning to thoughtful editorial packaging, publishing platforms restore a respectful contract with their readers' most precious asset: their undivided attention.</p>
    `
  },

  // 13. Technology (Author 3: Dr. Sarah)
  {
    id: 13,
    slug: 'local-first-software-and-user-data-sovereignty',
    title: 'Local-First Software and User Data Sovereignty',
    excerpt: 'Architectural paradigms that return data ownership to client devices using CRDTs and peer-to-peer sync mechanisms.',
    category: 'Technology',
    categorySlug: 'technology',
    tags: ['Local-First', 'CRDT', 'Software', 'Privacy'],
    authorId: 3,
    date: 'Aug 30, 2026',
    readingTime: '7 min read',
    views: 3120,
    likes: 198,
    commentCount: 0,
    image: 'https://picsum.photos/seed/tech-local-13/1000/600',
    content: `
      <h2>The Frailty of Pure Cloud Dependency</h2>
      <p>Cloud SaaS applications revolutionized real-time collaboration, but they introduced a troubling compromise: users no longer owned their files. When servers go offline or vendor pricing changes overnight, entire enterprise workflows grind to an abrupt halt.</p>

      <h3>Conflict-Free Replicated Data Types</h3>
      <p>Local-first software combines the autonomy of classic desktop software with the seamless collaboration of the modern cloud. By utilizing Conflict-Free Replicated Data Types (CRDTs), client devices read and write instantly to local storage without network roundtrips:</p>

      <pre><code class="language-javascript">// Optimistic local write with eventual peer convergence
doc.change(d => {
  d.posts.push({ id: 104, title: 'Sovereign Data' });
});</code></pre>

      <p>When connectivity resumes, updates merge deterministically without data loss, delivering blazing-fast snappy user interfaces that work flawlessly offline.</p>
    `
  },

  // 14. Education (Author 4: Mateo)
  {
    id: 14,
    slug: 'the-revival-of-apprenticeship-in-technical-crafts',
    title: 'The Revival of Apprenticeship in Technical Crafts',
    excerpt: 'Why experiential mentorship and cohort-based studio models are outpacing traditional lecture halls in technical professions.',
    category: 'Education',
    categorySlug: 'education',
    tags: ['Mentorship', 'Career', 'Craft', 'HigherEd'],
    authorId: 4,
    date: 'Aug 28, 2026',
    readingTime: '5 min read',
    views: 2190,
    likes: 134,
    commentCount: 0,
    image: 'https://picsum.photos/seed/edu-apprentice-14/1000/600',
    content: `
      <h2>The Limits of Passive Lecture Consumption</h2>
      <p>Watching pre-recorded video modules and memorizing multiple-choice rubrics creates an illusion of competence that quickly crumbles upon encountering messy production codebases or intricate manufacturing tolerances.</p>

      <h3>The Power of Studio Critique</h3>
      <p>Revived apprenticeship models pair emerging practitioners directly with seasoned masters in intense, project-focused studios. Regular code and design critiques uncover tacit knowledge—intuitions that cannot be codified into multiple-choice examinations:</p>

      <blockquote>
        "You do not learn how to navigate catastrophic system outages from a lecture slide; you learn by sitting beside an expert who remains calm while diagnosing trace telemetry."
      </blockquote>

      <p>Graduates of rigorous studio apprenticeships enter industry with battle-tested resilience and an intuitive grasp of professional team dynamics.</p>
    `
  },

  // 15. Travel (Author 5: Camille)
  {
    id: 15,
    slug: 'alpine-sanctuaries-and-the-silence-of-the-val-d-herens',
    title: 'Alpine Sanctuaries and the Silence of the Val d’Hérens',
    excerpt: 'Exploring remote Swiss side-valleys where wooden raccards, glacial streams, and high pastures preserve timeless mountain heritage.',
    category: 'Travel',
    categorySlug: 'travel',
    tags: ['Alps', 'Switzerland', 'Hiking', 'Nature'],
    authorId: 5,
    date: 'Aug 25, 2026',
    readingTime: '6 min read',
    views: 2840,
    likes: 189,
    commentCount: 0,
    image: 'https://picsum.photos/seed/travel-swiss-15/1000/600',
    content: `
      <h2>Beyond the Tourist High Resorts</h2>
      <p>While Zermatt and St. Moritz attract international throngs with luxury boutiques and cable cars, the rugged Val d’Hérens remains wonderfully uncorrupted. Here, traditional blackened larchwood cabins rest balanced upon mushroom-shaped stone stilts designed centuries ago to deter rodents.</p>

      <h3>The Majesty of Glacial Solitude</h3>
      <p>Ascending through high larch forests to the tongue of the Ferpècle Glacier, travelers are enveloped by a profound alpine stillness broken only by whistling marmots and rushing meltwater cascades:</p>

      <blockquote>
        "In the high mountains, silence is not an absence of sound, but an overwhelming presence of ancient stone and weather."
      </blockquote>

      <p>Staying in family-run mountain refuges and sampling raw-milk raclette cheese supports high-altitude pastoral farming that protects these fragile valleys from erosion.</p>
    `
  },

  // 16. Food (Author 6: Julian)
  {
    id: 16,
    slug: 'the-renaissance-of-japanese-koji-fermentation',
    title: 'The Renaissance of Japanese Koji Fermentation',
    excerpt: 'How Aspergillus oryzae transforms humble grains and legumes into the cornerstone of umami in contemporary fine dining.',
    category: 'Food',
    categorySlug: 'food',
    tags: ['Fermentation', 'Koji', 'Japanese Cuisine', 'Gastronomy'],
    authorId: 6,
    date: 'Aug 22, 2026',
    readingTime: '7 min read',
    views: 3450,
    likes: 228,
    commentCount: 0,
    image: 'https://picsum.photos/seed/food-koji-16/1000/600',
    content: `
      <h2>The National Microorganism of Japan</h2>
      <p>At the foundation of Japanese culinary genius stands <em>Aspergillus oryzae</em>—a filamentous fungus cultured onto steamed rice, barley, or soybeans. This microscopic alchemist produces potent amylase and protease enzymes that unlock intoxicating savory depths.</p>

      <h3>Global Kitchen Adaptations</h3>
      <p>Chefs across Copenhagen, New York, and São Paulo are applying traditional koji techniques to regional ingredients—fermenting sunflower seed misos, mushroom garums, and koji-cured charcuterie:</p>

      <blockquote>
        "Koji does not impose a flavor; it coaxes out the hidden, profound sweetness and umami inherent within the base ingredient."
      </blockquote>

      <p>Understanding and nurturing live enzymatic fermentations represents a creative frontier that elevates both sustainability and gastronomic artistry.</p>
    `
  },

  // 17. Business (Author 7: Amina)
  {
    id: 17,
    slug: 'the-death-of-middle-management-by-autonomous-tooling',
    title: 'The Death of Middle Management by Autonomous Tooling',
    excerpt: 'How automated asynchronous status trackers and direct documentation pipelines are flattening modern corporate hierarchies.',
    category: 'Business',
    categorySlug: 'business',
    tags: ['Management', 'Future of Work', 'Remote Work', 'Culture'],
    authorId: 7,
    date: 'Aug 19, 2026',
    readingTime: '8 min read',
    views: 4510,
    likes: 295,
    commentCount: 0,
    image: 'https://picsum.photos/seed/biz-mgmt-17/1000/600',
    content: `
      <h2>The Cost of Bureaucratic Coordination</h2>
      <p>In traditional corporate pyramids, vast layers of middle managers spent forty percent of their working hours summarizing tickets, scheduling status syncs, and compiling slide decks for senior executives. These feedback loops slowed decision velocity to a crawl.</p>

      <h3>Asynchronous Telemetry and High-Trust Teams</h3>
      <p>Modern remote-first enterprises are replacing synchronous status check-ins with automated telemetry feeds, continuous RFC document reviews, and transparent task boards:</p>

      <blockquote>
        "When engineering and product decisions happen transparently in written documentation, coordination overhead evaporates."
      </blockquote>

      <p>Individual contributors enjoy unprecedented operational autonomy, while organizations pivot in days rather than quarters.</p>
    `
  },

  // 18. Sports (Author 8: Dr. Aris)
  {
    id: 18,
    slug: 'altitude-acclimatization-and-hematological-adaptation',
    title: 'Altitude Acclimatization and Hematological Adaptation',
    excerpt: 'The cellular cascade of erythropoietin production, capillary density expansion, and oxygen delivery under hypoxic pressure.',
    category: 'Sports',
    categorySlug: 'sports',
    tags: ['Physiology', 'Altitude', 'Endurance', 'Training'],
    authorId: 8,
    date: 'Aug 16, 2026',
    readingTime: '6 min read',
    views: 2790,
    likes: 167,
    commentCount: 0,
    image: 'https://picsum.photos/seed/sports-altitude-18/1000/600',
    content: `
      <h2>Surviving Hypoxia</h2>
      <p>At altitudes above 2,500 meters, the partial pressure of oxygen decreases significantly. The body responds with rapid hyperventilation and elevated resting heart rates to maintain arterial oxygen saturation.</p>

      <h3>The "Live High, Train Low" Protocol</h3>
      <p>Within hours of hypoxic exposure, kidney interstitial fibroblasts trigger the synthesis of endogenous erythropoietin (EPO), stimulating bone marrow reticulocyte release:</p>

      <blockquote>
        "By living at 2,200 meters and descending to sea level for maximal tempo intervals, endurance athletes maximize red blood cell mass without sacrificing neuromuscular velocity."
      </blockquote>

      <p>Careful management of ferritin reserves and resting heart-rate variability ensures adaptations occur without precipitating central overtraining syndrome.</p>
    `
  },

  // 19. Health (Author 9: Priya)
  {
    id: 19,
    slug: 'the-neurobiology-of-chronic-stress-and-vagal-tone',
    title: 'The Neurobiology of Chronic Stress and Vagal Tone',
    excerpt: 'Understanding how the parasympathetic nervous system modulates heart rate variability and systemic immune homeostasis.',
    category: 'Health',
    categorySlug: 'health',
    tags: ['Neuroscience', 'Stress', 'Vagus Nerve', 'Mental Health'],
    authorId: 9,
    date: 'Aug 12, 2026',
    readingTime: '7 min read',
    views: 4190,
    likes: 310,
    commentCount: 0,
    image: 'https://picsum.photos/seed/health-stress-19/1000/600',
    content: `
      <h2>The Wear and Tear of Allostatic Load</h2>
      <p>The mammalian sympathetic nervous system evolved for acute survival emergencies—fleeing predators or enduring severe weather. In modern life, however, prolonged cognitive stress keeps circulating cortisol and inflammatory cytokines perpetually elevated.</p>

      <h3>Training the Vagus Brake</h3>
      <p>The vagus nerve serves as the primary conduit for the parasympathetic system. Interventions such as cyclic physiological sighs (two quick inhales through the nose followed by an extended audible exhale) stimulate pulmonary baroreceptors:</p>

      <blockquote>
        "Controlled extended exhalations trigger acetylcholine release across cardiac tissue, rapidly dialing back heart rate within twenty seconds."
      </blockquote>

      <p>Regular vagal toning through cold face plunges, aerobic endurance, and intentional respiration builds resilient autonomic adaptability.</p>
    `
  },

  // 20. Lifestyle (Author 10: David)
  {
    id: 20,
    slug: 'the-lost-ritual-of-the-analog-journal',
    title: 'The Lost Ritual of the Analog Journal',
    excerpt: 'Why putting pen to cotton rag paper unknots complex emotions and sharpens long-range philosophical clarity.',
    category: 'Lifestyle',
    categorySlug: 'lifestyle',
    tags: ['Journaling', 'Writing', 'Mindfulness', 'Creativity'],
    authorId: 10,
    date: 'Aug 08, 2026',
    readingTime: '5 min read',
    views: 2980,
    likes: 195,
    commentCount: 0,
    image: 'https://picsum.photos/seed/life-journal-20/1000/600',
    content: `
      <h2>The Speed of Typing vs. The Velocity of Thought</h2>
      <p>A keyboard allows humans to transcribe words at 80 words per minute. While advantageous for business correspondence, this speed frequently outpaces reflective contemplation. Hand-written journaling, by contrast, restricts output to 20 words per minute, forcing the mind to curate and weigh every sentence.</p>

      <h3>The Permanence of Ink</h3>
      <p>There is no backspace key in a Smyth-sewn leather notebook. Crossed-out words and margin annotations bear honest testament to the author’s evolving mental models:</p>

      <blockquote>
        "Writing by hand is a form of cognitive meditation. The physical friction of nib on paper demands that you inhabit the present moment."
      </blockquote>

      <p>Dedicate twenty minutes at dusk with a fountain pen and a cup of herbal tea to reflect honestly upon your challenges and gratitude.</p>
    `
  },

  // 21. Finance (Author 1: Elena)
  {
    id: 21,
    slug: 'the-geometry-of-debt-cycles-and-capital-preservation',
    title: 'The Geometry of Debt Cycles and Capital Preservation',
    excerpt: 'Deconstructing long-term deleveraging phases, fiat depreciation pressures, and non-correlated asset allocations for uncertain decades.',
    category: 'Finance',
    categorySlug: 'finance',
    tags: ['Finance', 'Macro', 'Investing', 'Economics'],
    authorId: 1,
    date: 'Aug 04, 2026',
    readingTime: '8 min read',
    views: 3740,
    likes: 240,
    commentCount: 0,
    image: 'https://picsum.photos/seed/fin-debt-21/1000/600',
    content: `
      <h2>The Inevitable Arc of Credit Expansion</h2>
      <p>Credit expansion stimulates economic velocity in early cycles, allowing production capacity to outrun immediate savings. Over decades, however, debt growth inevitably outpaces real productivity gains, requiring central banks to choose between austerity or currency debasement.</p>

      <h3>Asymmetric Capital Defense</h3>
      <p>During late-stage sovereign debt cycles, holding cash guarantees a steady erosion of purchasing power. Savvy institutional treasuries construct resilient all-weather portfolios combining hard monetary commodities, short-duration inflation-protected bonds, and capital-light productive monopolies:</p>

      <blockquote>
        "The first rule of long-term compounding is not chasing the highest speculative yield; it is avoiding catastrophic drawdowns during deleveraging earthquakes."
      </blockquote>

      <p>Maintaining liquidity reserves and practicing disciplined portfolio rebalancing preserves generational wealth through economic regime shifts.</p>
    `
  },

  // 22. Science (Author 2: Marcus)
  {
    id: 22,
    slug: 'synthetic-biology-and-the-design-of-carbon-negative-enzymes',
    title: 'Synthetic Biology and the Design of Carbon-Negative Enzymes',
    excerpt: 'Engineered biocatalysts capable of fixing atmospheric carbon directly into biodegradable industrial polymers.',
    category: 'Science',
    categorySlug: 'science',
    tags: ['Biology', 'Climate Tech', 'Genetics', 'Sustainability'],
    authorId: 2,
    date: 'Aug 01, 2026',
    readingTime: '7 min read',
    views: 4180,
    likes: 325,
    commentCount: 0,
    image: 'https://picsum.photos/seed/sci-synbio-22/1000/600',
    content: `
      <h2>Nature’s Under-Optimized Photochemistry</h2>
      <p>For three billion years, the enzyme Rubisco has powered terrestrial photosynthesis, yet it remains notoriously sluggish and prone to binding oxygen instead of carbon dioxide. Using deep generative structural models, bioengineers are designing synthetic carboxylation pathways that operate with three times natural efficiency.</p>

      <h3>From Petrochemicals to Microbial Fermentation</h3>
      <p>By transplanting these bespoke metabolic pathways into autotrophic cyanobacteria, industrial pilot plants are producing durable construction plastics directly from smokestack flue gas:</p>

      <blockquote>
        "We are transitioning from an extractive petroleum chemistry to an architectural biochemistry that heals the atmosphere with every barrel synthesized."
      </blockquote>

      <p>Scaling these biological carbon sinks represents one of the most promising technological frontiers for achieving global industrial net-negativity.</p>
    `
  },

  // 23. Art (Author 3: Dr. Sarah)
  {
    id: 23,
    slug: 'the-poetics-of-brutalist-architecture-in-eastern-europe',
    title: 'The Poetics of Brutalist Architecture in Eastern Europe',
    excerpt: 'Re-evaluating exposed board-marked concrete monuments as visionary monuments of civic optimism and community solidarity.',
    category: 'Art',
    categorySlug: 'art',
    tags: ['Architecture', 'Brutalism', 'Art History', 'Concrete'],
    authorId: 3,
    date: 'Jul 28, 2026',
    readingTime: '6 min read',
    views: 3150,
    likes: 210,
    commentCount: 0,
    image: 'https://picsum.photos/seed/art-brutalism-23/1000/600',
    content: `
      <h2>The Misunderstood Monumentalism of Béton Brut</h2>
      <p>Popular discourse frequently dismisses mid-century brutalist structures as bleak or dystopian. Yet in their inception, these soaring concrete edifices—from the Spomeniks of former Yugoslavia to the communal housing blocks of Warsaw—represented an audacious civic embrace of public space.</p>

      <h3>Sculpting Light with Raw Aggregate</h3>
      <p>Seen in the shifting light of late afternoon, board-marked concrete surfaces reveal rich textural topography. Deep cantilevered balconies cast dramatic chiaroscuro shadows across monumental civic plazas:</p>

      <blockquote>
        "Concrete is artificial rock made by human hands. When left honest and unadorned, it echoes the permanence and dignity of ancient geologic strata."
      </blockquote>

      <p>Architectural preservationists are organizing to protect these historic structures before commercial redevelopment erases their idealistic civic footprints.</p>
    `
  },

  // 24. Culture (Author 4: Mateo)
  {
    id: 24,
    slug: 'the-sociology-of-the-third-place-in-modern-cities',
    title: 'The Sociology of the Third Place in Modern Cities',
    excerpt: 'How public libraries, neighborhood chess parks, and community coffeehouses safeguard civic cohesion in fragmented times.',
    category: 'Culture',
    categorySlug: 'culture',
    tags: ['Urbanism', 'Sociology', 'Community', 'Public Spaces'],
    authorId: 4,
    date: 'Jul 24, 2026',
    readingTime: '7 min read',
    views: 4890,
    likes: 342,
    commentCount: 0,
    image: 'https://picsum.photos/seed/culture-third-place-24/1000/600',
    content: `
      <h2>Where Civil Society Breathes</h2>
      <p>Sociologist Ray Oldenburg coined the term "Third Place" to describe anchors of community life distinct from home (the first place) and work (the second place). These environments host the regular, voluntary, and delightfully informal gatherings of individuals.</p>

      <h3>The Erosion of Non-Commercial Commons</h3>
      <p>When municipal planners prioritize privatized retail developments over open public plazas, civic serendipity withers. True third places welcome people regardless of economic status, allowing retired elders, students, and professionals to converse across traditional demographic silos:</p>

      <blockquote>
        "A healthy city is not measured by the speed of its vehicular freeways, but by the vibrancy of the benches where strangers converse without a transactional agenda."
      </blockquote>

      <p>Investing in free public libraries, walkable pocket parks, and shaded pedestrian boulevards is an essential prerequisite for enduring democratic vitality.</p>
    `
  }
];

/**
 * 55 Comments Distributed Across 11 Articles (Counts match article.commentCount):
 * - Article 1: 7 comments
 * - Article 2: 6 comments
 * - Article 3: 5 comments
 * - Article 4: 6 comments
 * - Article 5: 5 comments
 * - Article 6: 4 comments
 * - Article 7: 5 comments
 * - Article 8: 4 comments
 * - Article 9: 5 comments
 * - Article 10: 4 comments
 * - Article 11: 4 comments
 * Total = 55 comments!
 */
const comments = [
  // Article 1 (7 comments)
  { id: 1, articleId: 1, authorName: 'Sofia Rivera', authorAvatar: 'https://i.pravatar.cc/100?img=1', date: 'Sep 25, 2026', content: 'This resonated deeply. We recently stripped three build steps from our company blog and page load dropped by 65%. Pure CSS variables are criminally underrated.', likes: 14 },
  { id: 2, articleId: 1, authorName: 'Klaus Lindner', authorAvatar: 'https://i.pravatar.cc/100?img=2', date: 'Sep 25, 2026', content: 'The point about WCAG AA compliance being baked into design tokens from day one is essential. Excellent article, Elena.', likes: 8 },
  { id: 3, articleId: 1, authorName: 'Maya Patel', authorAvatar: 'https://i.pravatar.cc/100?img=3', date: 'Sep 26, 2026', content: 'How do you handle spacing scaling when dealing with ultra-wide screens over 1600px? Still sticking to the 8px multiple?', likes: 4 },
  { id: 4, articleId: 1, authorName: 'Elena Rostova', authorAvatar: 'https://i.pravatar.cc/100?img=47', date: 'Sep 26, 2026', content: '@Maya Patel Yes! On ultra-wide we cap container max-width at 1320px and scale padding using clamp() with base 8px multiples. Prevents awkward line-lengths!', likes: 19 },
  { id: 5, articleId: 1, authorName: 'Tariq Al-Mansoor', authorAvatar: 'https://i.pravatar.cc/100?img=5', date: 'Sep 27, 2026', content: 'Clean, elegant, and actionable. Bookmarked for my team’s architecture review.', likes: 6 },
  { id: 6, articleId: 1, authorName: 'Jessica Walsh', authorAvatar: 'https://i.pravatar.cc/100?img=6', date: 'Sep 27, 2026', content: 'Fraunces paired with Inter is such a tasteful pairing. Very warm editorial feel.', likes: 11 },
  { id: 7, articleId: 1, authorName: 'Arthur Dent', authorAvatar: 'https://i.pravatar.cc/100?img=7', date: 'Sep 28, 2026', content: 'The dark mode palette transition without any flash is buttery smooth.', likes: 5 },

  // Article 2 (6 comments)
  { id: 8, articleId: 2, authorName: 'Hannah Abbott', authorAvatar: 'https://i.pravatar.cc/100?img=8', date: 'Sep 23, 2026', content: 'As a university lecturer, I see students struggling with screen fatigue daily. Formatting and line measure make an astonishing difference.', likes: 12 },
  { id: 9, articleId: 2, authorName: 'Dr. Gregory House', authorAvatar: 'https://i.pravatar.cc/100?img=9', date: 'Sep 23, 2026', content: 'Non-linear conceptual graphs sound great in theory, but how do we assess standardized baseline retention across cohorts?', likes: 7 },
  { id: 10, articleId: 2, authorName: 'Marcus Chen', authorAvatar: 'https://i.pravatar.cc/100?img=12', date: 'Sep 24, 2026', content: '@Dr. Gregory House Through progressive mastery milestones rather than rigid chapter end dates. We are seeing 40% higher retention in trials.', likes: 15 },
  { id: 11, articleId: 2, authorName: 'Li Wei', authorAvatar: 'https://i.pravatar.cc/100?img=11', date: 'Sep 24, 2026', content: 'Spaced repetition modules embedded right beside the text is the future of digital textbooks.', likes: 9 },
  { id: 12, articleId: 2, authorName: 'Claire Beauchamp', authorAvatar: 'https://i.pravatar.cc/100?img=13', date: 'Sep 25, 2026', content: 'I appreciate the emphasis on low-bandwidth mobile performance. Not all students have fiber connections.', likes: 14 },
  { id: 13, articleId: 2, authorName: 'Dante Rossi', authorAvatar: 'https://i.pravatar.cc/100?img=14', date: 'Sep 25, 2026', content: 'Fascinating thesis Marcus. Truly inspiring work.', likes: 3 },

  // Article 3 (5 comments)
  { id: 14, articleId: 3, authorName: 'Ingrid Bergman', authorAvatar: 'https://i.pravatar.cc/100?img=16', date: 'Sep 21, 2026', content: 'Pučišća on Brač is heaven on earth. Visited in 2023 and the quiet stone streets left an indelible mark on my heart.', likes: 16 },
  { id: 15, articleId: 3, authorName: 'Carlos Silva', authorAvatar: 'https://i.pravatar.cc/100?img=17', date: 'Sep 21, 2026', content: 'Slow travel requires unlearning our corporate vacation mindset. You don’t need to see five museums a day.', likes: 11 },
  { id: 16, articleId: 3, authorName: 'Sarah Jenkins', authorAvatar: 'https://i.pravatar.cc/100?img=32', date: 'Sep 22, 2026', content: '@Carlos Silva Exactly right. Sitting on a stone harbor wall for three hours watching fishermen is the actual vacation.', likes: 21 },
  { id: 17, articleId: 3, authorName: 'Ananya Roy', authorAvatar: 'https://i.pravatar.cc/100?img=18', date: 'Sep 22, 2026', content: 'The photography accompanying this piece is breathtaking.', likes: 5 },
  { id: 18, articleId: 3, authorName: 'Nils Holgersson', authorAvatar: 'https://i.pravatar.cc/100?img=19', date: 'Sep 23, 2026', content: 'Adding this entire itinerary to my autumn cycling voyage.', likes: 7 },

  // Article 4 (6 comments)
  { id: 19, articleId: 4, authorName: 'Lucia Moretti', authorAvatar: 'https://i.pravatar.cc/100?img=20', date: 'Sep 19, 2026', content: 'Einkorn flour has a noticeably nutty aroma. It handles differently due to weaker gluten, but the flavor is incomparable.', likes: 13 },
  { id: 20, articleId: 4, authorName: 'Henrik Vanger', authorAvatar: 'https://i.pravatar.cc/100?img=21', date: 'Sep 19, 2026', content: 'Do you recommend autolyse when working with 100% whole grain Emmer flour?', likes: 4 },
  { id: 21, articleId: 4, authorName: 'Mateo Morales', authorAvatar: 'https://i.pravatar.cc/100?img=60', date: 'Sep 20, 2026', content: '@Henrik Vanger Absolutely, but keep it brief (30-45 mins) and use cool water (20°C) to prevent enzymatic breakdown.', likes: 18 },
  { id: 22, articleId: 4, authorName: 'Beatriz Gomez', authorAvatar: 'https://i.pravatar.cc/100?img=22', date: 'Sep 20, 2026', content: 'Commercial fast-yeast bread makes me bloated. Sourdough long ferment never does. Science explains why.', likes: 22 },
  { id: 23, articleId: 4, authorName: 'Samir Nasri', authorAvatar: 'https://i.pravatar.cc/100?img=23', date: 'Sep 21, 2026', content: 'Such an informative deep dive into bread chemistry.', likes: 6 },
  { id: 24, articleId: 4, authorName: 'Grace Hopper', authorAvatar: 'https://i.pravatar.cc/100?img=24', date: 'Sep 21, 2026', content: 'Now I urgently need a warm loaf with salted butter.', likes: 15 },

  // Article 5 (5 comments)
  { id: 25, articleId: 5, authorName: 'Warren Sterling', authorAvatar: 'https://i.pravatar.cc/100?img=25', date: 'Sep 17, 2026', content: 'Finally someone says it plainly. Subsidizing CAC with venture debt and calling it product-market fit was peak absurdity.', likes: 31 },
  { id: 26, articleId: 5, authorName: 'Nadia Petrova', authorAvatar: 'https://i.pravatar.cc/100?img=27', date: 'Sep 17, 2026', content: 'Bootstrapped founders are having their vindication moment right now.', likes: 19 },
  { id: 27, articleId: 5, authorName: 'Camille Laurent', authorAvatar: 'https://i.pravatar.cc/100?img=49', date: 'Sep 18, 2026', content: '@Nadia Petrova Unquestionably. Sustainable gross margins build unshakeable longevity.', likes: 24 },
  { id: 28, articleId: 5, authorName: 'Ethan Hunt', authorAvatar: 'https://i.pravatar.cc/100?img=28', date: 'Sep 18, 2026', content: 'What is your view on SaaS net revenue retention benchmarks in this climate? Is 110% still acceptable?', likes: 8 },
  { id: 29, articleId: 5, authorName: 'Zoe Saldana', authorAvatar: 'https://i.pravatar.cc/100?img=29', date: 'Sep 19, 2026', content: 'Great insights for our quarterly board meeting preparation.', likes: 5 },

  // Article 6 (4 comments)
  { id: 30, articleId: 6, authorName: 'Kenenisa Bekele Fan', authorAvatar: 'https://i.pravatar.cc/100?img=30', date: 'Sep 15, 2026', content: 'The aerodynamic drafting calculations in this article were eye-opening. The arrow formation works wonders.', likes: 14 },
  { id: 31, articleId: 6, authorName: 'Dr. Timothy Noakes', authorAvatar: 'https://i.pravatar.cc/100?img=31', date: 'Sep 15, 2026', content: 'The Central Governor Model still applies. The brain will shut down the muscle fibers before mechanical failure occurs.', likes: 17 },
  { id: 32, articleId: 6, authorName: 'Julian Vance', authorAvatar: 'https://i.pravatar.cc/100?img=53', date: 'Sep 16, 2026', content: 'Agreed Dr. Noakes, that’s why mental conditioning and pacing telemetry must co-evolve.', likes: 10 },
  { id: 33, articleId: 6, authorName: 'Rachel Green', authorAvatar: 'https://i.pravatar.cc/100?img=33', date: 'Sep 16, 2026', content: 'Super shoes shaved 9 minutes off my Boston marathon qualifier!', likes: 23 },

  // Article 7 (5 comments)
  { id: 34, articleId: 7, authorName: 'Andrew Huberman Fan', authorAvatar: 'https://i.pravatar.cc/100?img=34', date: 'Sep 13, 2026', content: 'Morning photons into the eyes within 30 mins changed my life. No more 3 PM energy slumps.', likes: 27 },
  { id: 35, articleId: 7, authorName: 'Dr. Rhonda Patrick', authorAvatar: 'https://i.pravatar.cc/100?img=35', date: 'Sep 13, 2026', content: 'The molecular interaction between peripheral liver clocks and SCN master regulation is described with absolute clarity.', likes: 35 },
  { id: 36, articleId: 7, authorName: 'Amina Nour', authorAvatar: 'https://i.pravatar.cc/100?img=26', date: 'Sep 14, 2026', content: 'Thank you Dr. Rhonda! Honored by your feedback.', likes: 18 },
  { id: 37, articleId: 7, authorName: 'Marcus Aurelius Fan', authorAvatar: 'https://i.pravatar.cc/100?img=36', date: 'Sep 14, 2026', content: 'Discipline in sleep leads to discipline in mind.', likes: 12 },
  { id: 38, articleId: 7, authorName: 'Chloe Bennett', authorAvatar: 'https://i.pravatar.cc/100?img=37', date: 'Sep 15, 2026', content: 'Sharing this with my medical school study group.', likes: 9 },

  // Article 8 (4 comments)
  { id: 39, articleId: 8, authorName: 'Cal Newport Reader', authorAvatar: 'https://i.pravatar.cc/100?img=38', date: 'Sep 11, 2026', content: 'Physical workspaces dictate cognitive states. Putting my smartphone in another room gave me my career back.', likes: 26 },
  { id: 40, articleId: 8, authorName: 'Dr. Aris Thorne', authorAvatar: 'https://i.pravatar.cc/100?img=15', date: 'Sep 11, 2026', content: 'Friction is the most effective behavioral design lever we have.', likes: 19 },
  { id: 41, articleId: 8, authorName: 'Emma Watson', authorAvatar: 'https://i.pravatar.cc/100?img=39', date: 'Sep 12, 2026', content: 'Warm lighting and paper notebooks truly cultivate a meditative atmosphere.', likes: 11 },
  { id: 42, articleId: 8, authorName: 'Oliver Twist', authorAvatar: 'https://i.pravatar.cc/100?img=40', date: 'Sep 12, 2026', content: 'Inspiring architecture photos in this article.', likes: 4 },

  // Article 9 (5 comments)
  { id: 43, articleId: 9, authorName: 'Satoshi Disciple', authorAvatar: 'https://i.pravatar.cc/100?img=41', date: 'Sep 09, 2026', content: 'DvP settlement on decentralized rails removes counterparty risk that has plagued clearing houses for decades.', likes: 22 },
  { id: 44, articleId: 9, authorName: 'Christine Lagarde Watch', authorAvatar: 'https://i.pravatar.cc/100?img=42', date: 'Sep 09, 2026', content: 'Regulatory compliance and AML checks will remain the sticking point for institutional adoption.', likes: 15 },
  { id: 45, articleId: 9, authorName: 'Priya Sharma', authorAvatar: 'https://i.pravatar.cc/100?img=44', date: 'Sep 10, 2026', content: 'Spot on @Christine. That is why programmable compliance tokens embedded into smart contracts are gaining traction.', likes: 20 },
  { id: 46, articleId: 9, authorName: 'Felix Baum', authorAvatar: 'https://i.pravatar.cc/100?img=43', date: 'Sep 10, 2026', content: 'The trapped liquidity numbers in nostro accounts are staggering.', likes: 8 },
  { id: 47, articleId: 9, authorName: 'Geoffrey Hinton', authorAvatar: 'https://i.pravatar.cc/100?img=45', date: 'Sep 11, 2026', content: 'Brilliant macroeconomic breakdown.', likes: 7 },

  // Article 10 (4 comments)
  { id: 48, articleId: 10, authorName: 'Carl Sagan Legacy', authorAvatar: 'https://i.pravatar.cc/100?img=46', date: 'Sep 07, 2026', content: 'Somewhere, something incredible is waiting to be known. JWST confirms that daily.', likes: 44 },
  { id: 49, articleId: 10, authorName: 'Kip Thorne Fan', authorAvatar: 'https://i.pravatar.cc/100?img=48', date: 'Sep 07, 2026', content: 'Primordial black holes as early seeds would solve both dark matter and supermassive galaxy timing.', likes: 29 },
  { id: 50, articleId: 10, authorName: 'David Kim', authorAvatar: 'https://i.pravatar.cc/100?img=68', date: 'Sep 08, 2026', content: 'Yes! The next spectroscopic dataset dropping in November will test that exact hypothesis.', likes: 16 },
  { id: 51, articleId: 10, authorName: 'Neil Tyson', authorAvatar: 'https://i.pravatar.cc/100?img=50', date: 'Sep 08, 2026', content: 'Astrophysics is living in its greatest renaissance right now.', likes: 18 },

  // Article 11 (4 comments)
  { id: 52, articleId: 11, authorName: 'Paula Scher Fan', authorAvatar: 'https://i.pravatar.cc/100?img=51', date: 'Sep 05, 2026', content: 'The texture of damp printmaking paper going through a heavy etching press cannot be simulated by any GPU.', likes: 25 },
  { id: 53, articleId: 11, authorName: 'Ansel Adams Spirit', authorAvatar: 'https://i.pravatar.cc/100?img=52', date: 'Sep 05, 2026', content: 'Silver gelatin darkroom prints hold depth that digital screens merely imitate.', likes: 19 },
  { id: 54, articleId: 11, authorName: 'Elena Rostova', authorAvatar: 'https://i.pravatar.cc/100?img=47', date: 'Sep 06, 2026', content: 'Exactly. Handcraft demands our physical presence and fallible humanity.', likes: 14 },
  { id: 55, articleId: 11, authorName: 'Yuki Takahashi', authorAvatar: 'https://i.pravatar.cc/100?img=54', date: 'Sep 06, 2026', content: 'Wabi-sabi aesthetics are the true antidote to synthetic image overload.', likes: 21 }
];

/**
 * 10 Testimonials (8 positive ratings 4-5, 2 balanced ratings of 3 for realism)
 */
const testimonials = [
  {
    id: 1,
    name: 'Dr. Leonard McCoy',
    role: 'Chief Medical Editor at HealthChronicle',
    avatar: 'https://i.pravatar.cc/120?img=55',
    rating: 5,
    quote: 'BlogHub provides an uncompromising editorial sanctuary. The typography, responsive hierarchy, and absence of clickbait makes it a premier destination for serious scientific writers.'
  },
  {
    id: 2,
    name: 'Rachel Sterling',
    role: 'Staff Product Designer at Figma',
    avatar: 'https://i.pravatar.cc/120?img=56',
    rating: 5,
    quote: 'The design tokens and serif typography pairing are magnificent. Reading on mobile during my morning commute is genuinely effortless.'
  },
  {
    id: 3,
    name: 'Victor Vance',
    role: 'Founder at Hyperion Capital',
    avatar: 'https://i.pravatar.cc/120?img=57',
    rating: 4,
    quote: 'High signal, low noise. In an internet polluted by algorithmic slop, BlogHub reminds me of the golden age of long-form intellectual periodicals.'
  },
  {
    id: 4,
    name: 'Tanya Morales',
    role: 'Independent Travel Journalist',
    avatar: 'https://i.pravatar.cc/120?img=58',
    rating: 5,
    quote: 'The author profile presentation gives my essays genuine dignity. It makes my independent research look as polished as a major print publication.'
  },
  {
    id: 5,
    name: 'Marcus Brody',
    role: 'Dean of Arts & Letters',
    avatar: 'https://i.pravatar.cc/120?img=59',
    rating: 3,
    quote: 'The article caliber is consistently commendable, although I would love to see more frequent releases in classical philosophy and archival literature.'
  },
  {
    id: 6,
    name: 'Claire Dupont',
    role: 'Culinary Anthropologist & Author',
    avatar: 'https://i.pravatar.cc/120?img=61',
    rating: 5,
    quote: 'Finding deep dives on heirloom fermentation alongside distributed software architecture under one roof is rare. A brilliant editorial vision.'
  },
  {
    id: 7,
    name: 'Arjun Sen',
    role: 'Senior Infrastructure Architect',
    avatar: 'https://i.pravatar.cc/120?img=62',
    rating: 4,
    quote: 'Clean syntax styling, fast page navigation, and zero render-blocking bloat. A developer’s dream reading platform.'
  },
  {
    id: 8,
    name: 'Helena Lindqvist',
    role: 'Ecological Urban Planner',
    avatar: 'https://i.pravatar.cc/120?img=63',
    rating: 5,
    quote: 'I use BlogHub essays regularly in my university seminars. The depth of research and clear citations make it an indispensable educational resource.'
  },
  {
    id: 9,
    name: 'Damian Cross',
    role: 'Technology Columnist',
    avatar: 'https://i.pravatar.cc/120?img=64',
    rating: 3,
    quote: 'High quality journalism and great dark mode styling, though I occasionally encounter categories that only have two or three featured articles.'
  },
  {
    id: 10,
    name: 'Sophia Chen',
    role: 'VP of Engineering at CloudWave',
    avatar: 'https://i.pravatar.cc/120?img=65',
    rating: 5,
    quote: 'BlogHub has become mandatory reading across our engineering organization. It respects the reader’s intelligence and time.'
  }
];

// Export to Global Window Scope for Client-Side Blade Views
if (typeof window !== 'undefined') {
  window.blogHubData = {
    categories,
    authors,
    articles,
    comments,
    testimonials
  };
}

if (typeof globalThis !== 'undefined') {
  globalThis.blogHubData = {
    categories,
    authors,
    articles,
    comments,
    testimonials
  };
}

// Node.js / CommonJS / Module Export support for testing
if (typeof module !== 'undefined' && module.exports) {
  module.exports = {
    categories,
    authors,
    articles,
    comments,
    testimonials
  };
}

// Note: Loaded as a classic <script> tag — window.blogHubData is set above.

