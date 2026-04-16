export const experienceData = {
  id: 'exp-123',
  title: 'Authentic Tuscan Pasta Making with a Local Nonna',
  category: 'Culinary',
  location: 'Florence, Italy',
  duration: '3 hours',
  groupSize: 'Max 6 people',
  languages: ['English', 'Italian'],
  price: 85,
  currency: '€',
  images: [
    'https://images.unsplash.com/photo-1556761223-4c4282c73f77?auto=format&fit=crop&q=80&w=1200',
    'https://images.unsplash.com/photo-1598511796318-7b82ef4a6eb1?auto=format&fit=crop&q=80&w=600',
    'https://images.unsplash.com/photo-1608897013039-887f21d8c804?auto=format&fit=crop&q=80&w=600',
    'https://images.unsplash.com/photo-1587424161983-490327179069?auto=format&fit=crop&q=80&w=600',
    'https://images.unsplash.com/photo-1473093295043-cdd812d0e601?auto=format&fit=crop&q=80&w=600'
  ],
  host: {
    id: 'host-456',
    name: 'Elena Rossi',
    avatar: 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200',
    verified: true,
    responseRate: '100%',
    responseTime: 'within an hour',
    languages: 'English, Italian',
    bio: 'Ciao! I\\'m Elena, born and raised in Florence. Cooking is my passion, passed down from my grandmother. I love sharing authentic Tuscan recipes and the stories behind them with travelers from all over the world in my historic family home.',
  },
  overview: 'Step into a traditional Florentine kitchen and learn the secrets of perfect handmade pasta. We\\'ll start with a welcome glass of Chianti, then roll up our sleeves to make three types of pasta from scratch: tagliatelle, ravioli, and tortellini, followed by enjoying our creations together.',
  itinerary: [
    { time: '10:00 AM', title: 'Welcome & Aperitivo', description: 'Meet at my home, enjoy local wine, meats and cheeses while getting to know each other.' },
    { time: '10:30 AM', title: 'Pasta Dough Masterclass', description: 'Learn to mix and knead the perfect egg pasta dough.' },
    { time: '11:30 AM', title: 'Shaping & Filling', description: 'Create tagliatelle, and craft ricotta-filled ravioli and tortellini.' },
    { time: '12:30 PM', title: 'The Feast', description: 'Sit down at the family dining table to enjoy the pasta we made, paired with local wines.' }
  ],
  included: ['All ingredients and equipment', '3 types of pasta', 'Local wine and water', 'Aperitivo (cheeses, meats, olives)', 'Digital recipe book'],
  meetingPoint: 'Piazza del Carmine, 14, 50124 Firenze FI, Italy. Look for the green wooden door next to the bakery.',
  reviews: {
    average: 4.96,
    total: 124,
    breakdown: { 5: 115, 4: 7, 3: 2, 2: 0, 1: 0 },
    samples: [
      { id: 1, author: 'Sarah Jenkins', avatar: 'https://i.pravatar.cc/150?u=sarah', country: '🇺🇸', date: 'October 2023', text: 'Elena was the perfect host! Her home is beautiful and the pasta was the best I had in Italy. Highly recommend this for anyone visiting Florence.' },
      { id: 2, author: 'David Chen', avatar: 'https://i.pravatar.cc/150?u=david', country: '🇨🇦', date: 'September 2023', text: 'Such a heartfelt experience. Elena makes you feel like family. The ravioli with sage butter was incredible.' },
      { id: 3, author: 'Emma & Tom', avatar: 'https://i.pravatar.cc/150?u=emma', country: '🇬🇧', date: 'August 2023', text: 'A highlight of our honeymoon. Fantastic food, endless wine, and Elena is hilarious and warm.' },
      { id: 4, author: 'Lukas Schmidt', avatar: 'https://i.pravatar.cc/150?u=lukas', country: '🇩🇪', date: 'August 2023', text: 'Very authentic. I learned techniques I will definitely use back home. Thank you Elena!' },
      { id: 5, author: 'Maria Garcia', avatar: 'https://i.pravatar.cc/150?u=maria', country: '🇪🇸', date: 'July 2023', text: 'Wonderful morning. The group size (we were 5) was perfect for personalized attention.' }
    ]
  },
  similar: [
    { id: 'sim-1', title: 'Tuscan Truffle Hunting & Lunch', image: 'https://images.unsplash.com/photo-1542382103399-52e85eb66228?auto=format&fit=crop&q=80&w=400', price: 120, rating: 4.9, reviews: 88, location: 'San Miniato' },
    { id: 'sim-2', title: 'Gelato Masterclass in Rome', image: 'https://images.unsplash.com/photo-1579954115545-a95591f28b48?auto=format&fit=crop&q=80&w=400', price: 65, rating: 4.8, reviews: 210, location: 'Rome' },
    { id: 'sim-3', title: 'Street Food Tour by Vintage Vespa', image: 'https://images.unsplash.com/photo-1517524008697-84bbe3c3fd98?auto=format&fit=crop&q=80&w=400', price: 95, rating: 5.0, reviews: 156, location: 'Florence' }
  ]
};