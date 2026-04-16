import React, { useState } from 'react';
import BookingFlow from '../components/BookingFlow';
import { experienceData } from '../data/mockData';

export default function ExperienceDetail() {
  const [isBookingOpen, setIsBookingOpen] = useState(false);
  const [activeTab, setActiveTab] = useState('overview');
  const [isGalleryOpen, setIsGalleryOpen] = useState(false);
  const data = experienceData;

  const tabs = ['Overview', 'Itinerary', 'What\\'s Included', 'Meeting Point', 'Reviews'];

  return (
    <div className="bg-white min-h-screen text-gray-900 pb-24 md:pb-0">
      {/* Navbar placeholder */}
      <header className="border-b px-6 py-4 flex justify-between items-center z-40 relative bg-white">
        <div className="font-bold text-xl tracking-tight text-blue-600">WanderLocal</div>
        <div className="hidden md:flex gap-4 text-sm font-medium">
          <a href="#" className="hover:text-blue-600">Experiences</a>
          <a href="#" className="hover:text-blue-600">Hosts</a>
        </div>
      </header>

      <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {/* Title & Actions */}
        <div className="flex flex-col md:flex-row md:items-end justify-between mb-6 gap-4">
          <div>
            <div className="flex items-center gap-2 mb-2">
              <span className="bg-amber-100 text-amber-800 text-xs font-bold px-2 py-1 rounded uppercase tracking-wide">{data.category}</span>
              <span className="text-gray-500 text-sm flex items-center gap-1">📍 {data.location}</span>
            </div>
            <h1 className="text-3xl md:text-4xl font-extrabold tracking-tight mb-2 text-gray-900">{data.title}</h1>
            <div className="flex items-center text-sm gap-4 text-gray-600 font-medium">
              <span className="flex items-center gap-1">⭐ {data.reviews.average} ({data.reviews.total} reviews)</span>
              <span>⏱️ {data.duration}</span>
              <span>👥 {data.groupSize}</span>
            </div>
          </div>
          <div className="flex gap-2">
            <button className="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-gray-100 font-medium transition underline text-sm">
              📤 Share
            </button>
            <button className="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-gray-100 font-medium transition underline text-sm">
              ❤️ Save
            </button>
          </div>
        </div>

        {/* Photo Gallery Grid */}
        <div className="relative rounded-2xl overflow-hidden mb-12 group cursor-pointer" onClick={() => setIsGalleryOpen(true)}>
          <div className="grid grid-cols-1 md:grid-cols-4 grid-rows-2 h-[60vh] gap-2">
            <img src={data.images[0]} className="w-full h-full object-cover md:col-span-2 md:row-span-2 hover:opacity-95 transition" alt="Hero" />
            <img src={data.images[1]} className="hidden md:block w-full h-full object-cover hover:opacity-95 transition" alt="Thumbnail 1" />
            <img src={data.images[2]} className="hidden md:block w-full h-full object-cover hover:opacity-95 transition" alt="Thumbnail 2" />
            <img src={data.images[3]} className="hidden md:block w-full h-full object-cover hover:opacity-95 transition" alt="Thumbnail 3" />
            <img src={data.images[4]} className="hidden md:block w-full h-full object-cover hover:opacity-95 transition" alt="Thumbnail 4" />
          </div>
          <div className="absolute bottom-4 right-4 bg-white/90 backdrop-blur px-4 py-2 rounded-lg text-sm font-semibold shadow-sm border">
            Show all photos
          </div>
        </div>

        {/* Lightbox / Overlay Placeholder */}
        {isGalleryOpen && (
          <div className="fixed inset-0 bg-black z-50 flex items-center justify-center p-8">
            <button onClick={() => setIsGalleryOpen(false)} className="absolute top-6 right-6 text-white text-2xl">✕</button>
            <img src={data.images[0]} className="max-w-full max-h-full object-contain" alt="Fullscreen" />
          </div>
        )}

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-12 relative">
          {/* Main Content */}
          <div className="lg:col-span-2 space-y-12">
            
            {/* Host Card */}
            <div className="flex gap-6 items-center p-6 border rounded-2xl shadow-sm bg-gray-50/50">
              <img src={data.host.avatar} className="w-20 h-20 rounded-full object-cover shadow-sm border-2 border-white" alt={data.host.name} />
              <div className="flex-1">
                <div className="flex items-center gap-2 mb-1">
                  <h3 className="font-bold text-xl">Hosted by {data.host.name}</h3>
                  {data.host.verified && <span className="text-blue-600 bg-blue-50 px-2 py-0.5 rounded text-xs font-bold border border-blue-100">✓ Verified</span>}
                </div>
                <p className="text-gray-600 text-sm mb-3 max-w-lg">{data.host.bio}</p>
                <div className="flex flex-wrap text-sm gap-x-6 gap-y-2 text-gray-500 mb-4 font-medium">
                  <span className="flex items-center gap-1.5">💬 Responds {data.host.responseTime}</span>
                  <span className="flex items-center gap-1.5">⚡ {data.host.responseRate} rate</span>
                  <span className="flex items-center gap-1.5">🗣️ {data.host.languages}</span>
                </div>
                <button className="text-blue-600 font-semibold hover:underline text-sm">View Full Profile →</button>
              </div>
            </div>

            {/* Tabbed Navigation */}
            <div>
              <div className="border-b flex gap-6 overflow-x-auto sticky top-0 bg-white z-10 pt-4">
                {tabs.map(tab => (
                  <button
                    key={tab}
                    onClick={() => setActiveTab(tab.toLowerCase())}
                    className={`pb-4 whitespace-nowrap font-medium text-sm transition-colors relative ${
                      activeTab === tab.toLowerCase() ? 'text-gray-900 border-b-2 border-gray-900' : 'text-gray-500 hover:text-gray-800'
                    }`}
                  >
                    {tab}
                  </button>
                ))}
              </div>

              {/* Tab Content */}
              <div className="py-8 min-h-[300px]">
                {activeTab === 'overview' && (
                  <div className="prose text-gray-700 max-w-none text-lg leading-relaxed">
                    <p>{data.overview}</p>
                    <div className="mt-8 grid grid-cols-2 gap-4">
                      <div className="p-4 bg-gray-50 rounded-xl">
                        <div className="text-xs font-bold uppercase tracking-wide text-gray-500 mb-1">Languages</div>
                        <div className="font-medium">{data.languages.join(', ')}</div>
                      </div>
                      <div className="p-4 bg-gray-50 rounded-xl">
                        <div className="text-xs font-bold uppercase tracking-wide text-gray-500 mb-1">Duration</div>
                        <div className="font-medium">{data.duration}</div>
                      </div>
                    </div>
                  </div>
                )}

                {activeTab === 'itinerary' && (
                  <div className="space-y-6 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-gray-200 before:to-transparent">
                    {data.itinerary.map((item, idx) => (
                      <div key={idx} className="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                        <div className="flex items-center justify-center w-10 h-10 rounded-full border border-white bg-white shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 z-10">
                          <div className="w-3 h-3 bg-blue-600 rounded-full"></div>
                        </div>
                        <div className="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] bg-white p-5 rounded border shadow-sm">
                          <div className="text-xs font-bold text-blue-600 mb-1">{item.time}</div>
                          <h4 className="font-bold mb-1">{item.title}</h4>
                          <p className="text-sm text-gray-600">{item.description}</p>
                        </div>
                      </div>
                    ))}
                  </div>
                )}

                {activeTab === 'what\\'s included' && (
                  <ul className="space-y-4">
                    {data.included.map((item, idx) => (
                      <li key={idx} className="flex gap-4 p-4 border rounded-xl hover:border-gray-300 transition items-center text-gray-700">
                        <span className="text-green-500 text-xl font-bold bg-green-50 w-8 h-8 rounded flex items-center justify-center">✓</span> {item}
                      </li>
                    ))}
                  </ul>
                )}

                {activeTab === 'meeting point' && (
                  <div className="bg-gray-50 p-6 rounded-2xl border">
                    <div className="flex items-start gap-4 mb-4">
                      <span className="text-2xl mt-1">📍</span>
                      <div>
                        <h4 className="font-bold text-lg mb-1">Meet your host</h4>
                        <p className="text-gray-700">{data.meetingPoint}</p>
                      </div>
                    </div>
                    {/* Placeholder Map */}
                    <div className="w-full h-64 bg-gray-200 rounded-xl mt-6 flex items-center justify-center text-gray-500 font-mono text-sm border">
                      [ Interactive Map Overlay ]
                    </div>
                  </div>
                )}

                {activeTab === 'reviews' && (
                  <div>
                    <div className="flex items-center gap-6 mb-10 pb-10 border-b">
                      <div className="text-center">
                        <div className="text-5xl font-extrabold">{data.reviews.average}</div>
                        <div className="text-sm font-bold text-gray-500 uppercase tracking-widest mt-2">{data.reviews.total} Reviews</div>
                      </div>
                      <div className="flex-1 max-w-sm space-y-2">
                        {Object.entries(data.reviews.breakdown).reverse().map(([stars, count]) => (
                          <div key={stars} className="flex items-center text-sm">
                            <span className="w-4 font-bold text-gray-400">{stars}</span>
                            <span className="mx-2 text-yellow-400">★</span>
                            <div className="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                              <div className="h-full bg-gray-900 rounded-full" style={{ width: `${(count / data.reviews.total) * 100}%` }}></div>
                            </div>
                            <span className="w-8 text-right text-gray-400 text-xs font-mono">{count}</span>
                          </div>
                        ))}
                      </div>
                    </div>
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-10">
                      {data.reviews.samples.map(review => (
                        <div key={review.id} className="space-y-4">
                          <div className="flex items-center gap-4">
                            <img src={review.avatar} className="w-12 h-12 rounded-full object-cover" alt={review.author} />
                            <div>
                              <div className="font-bold flex items-center gap-2">{review.author} <span>{review.country}</span></div>
                              <div className="text-xs text-gray-500">{review.date}</div>
                            </div>
                          </div>
                          <p className="text-gray-700 leading-relaxed text-sm">{review.text}</p>
                        </div>
                      ))}
                    </div>
                    <button className="mt-8 px-6 py-3 border border-gray-900 rounded-xl font-bold hover:bg-gray-50 transition w-full md:w-auto">
                      Show all {data.reviews.total} reviews
                    </button>
                  </div>
                )}
              </div>
            </div>
            
            {/* Similar Experiences */}
            <div className="pt-12 border-t mt-12">
              <h2 className="text-2xl font-bold mb-6">You might also like</h2>
              <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                {data.similar.map(sim => (
                  <div key={sim.id} className="group cursor-pointer">
                    <div className="relative aspect-[4/3] rounded-xl overflow-hidden mb-3">
                      <img src={sim.image} className="w-full h-full object-cover group-hover:scale-105 transition duration-300" alt={sim.title} />
                      <button className="absolute top-3 right-3 p-1.5 text-white bg-black/20 rounded-full backdrop-blur-sm hover:bg-black/40"><svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg></button>
                    </div>
                    <div className="flex justify-between items-start">
                      <h3 className="font-medium text-sm line-clamp-2 leading-snug">{sim.title}</h3>
                      <span className="flex items-center gap-1 text-sm font-medium">★ {sim.rating}</span>
                    </div>
                    <p className="text-gray-500 text-sm mt-1">{sim.location}</p>
                    <p className="font-bold mt-1 text-sm"><span className="text-gray-900">€{sim.price}</span> <span className="text-gray-500 font-normal">/ person</span></p>
                  </div>
                ))}
              </div>
            </div>
          </div>

          {/* Sticky Booking Sidebar / Bottom Sheet */}
          <div className="hidden lg:block">
            <div className="sticky top-24 border rounded-2xl p-6 shadow-xl shadow-gray-200/50 bg-white">
              <div className="flex justify-between items-end mb-6">
                <div>
                  <span className="text-3xl font-extrabold text-gray-900">{data.currency}{data.price}</span>
                  <span className="text-gray-500 ml-1 font-medium">/ person</span>
                </div>
                <div className="text-sm flex items-center gap-1 font-medium hover:underline cursor-pointer"><span className="text-yellow-500">★</span> {data.reviews.average}</div>
              </div>
              
              <div className="border rounded-xl border-gray-300 overflow-hidden mb-4 shadow-sm">
                <div className="flex border-b border-gray-300">
                  <div className="p-3 w-1/2 border-r border-gray-300">
                    <label className="block text-[10px] font-bold uppercase tracking-wide text-gray-500 mb-1">Date</label>
                    <input type="date" className="w-full text-sm font-medium outline-none" defaultValue="2024-05-15" />
                  </div>
                  <div className="p-3 w-1/2">
                    <label className="block text-[10px] font-bold uppercase tracking-wide text-gray-500 mb-1">Time</label>
                    <select className="w-full text-sm font-medium outline-none bg-transparent">
                      <option>10:00 AM</option>
                      <option>2:00 PM</option>
                    </select>
                  </div>
                </div>
                <div className="p-3">
                  <label className="block text-[10px] font-bold uppercase tracking-wide text-gray-500 mb-1">Guests</label>
                  <select className="w-full text-sm font-medium outline-none bg-transparent">
                    <option>2 Guests</option>
                    <option>3 Guests</option>
                    <option>4 Guests</option>
                  </select>
                </div>
              </div>
              
              <button onClick={() => setIsBookingOpen(true)} className="w-full bg-blue-600 text-white py-3.5 rounded-xl font-bold text-lg hover:bg-blue-700 transition shadow-lg shadow-blue-600/20 mb-3 relative overflow-hidden group">
                <span className="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition"></span>
                <span className="relative">Book Now</span>
              </button>
              
              <button className="w-full py-3.5 border border-gray-300 rounded-xl font-bold text-gray-700 hover:bg-gray-50 transition">
                Message Host
              </button>

              <div className="mt-4 text-center text-xs text-gray-500 flex items-center justify-center gap-2">
                🔒 Secure payment • Instant confirmation
              </div>
            </div>
          </div>

          {/* Mobile Bottom Bar */}
          <div className="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t p-4 flex justify-between items-center z-40 shadow-[0_-10px_40px_rgba(0,0,0,0.1)]">
            <div>
              <div className="font-bold text-lg">{data.currency}{data.price} <span className="text-sm font-normal text-gray-500">/ person</span></div>
              <div className="text-xs text-gray-500 underline font-medium mt-0.5">{data.date}</div>
            </div>
            <button onClick={() => setIsBookingOpen(true)} className="bg-blue-600 px-8 py-3 rounded-xl text-white font-bold tracking-wide">
              Book Now
            </button>
          </div>
        </div>
      </main>

      {/* Booking Modal */}
      {isBookingOpen && (
        <BookingFlow experience={data} onClose={() => setIsBookingOpen(false)} />
      )}
    </div>
  );
}