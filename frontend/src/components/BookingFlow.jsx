import React, { useState } from 'react';

export default function BookingFlow({ experience, onClose }) {
  const [step, setStep] = useState(1);
  const [guests, setGuests] = useState(2);
  const [date, setDate] = useState('');
  const [details, setDetails] = useState({ name: '', email: '', phone: '', nationality: '', requests: '' });

  const total = experience.price * guests;

  const nextStep = (e) => {
    e.preventDefault();
    setStep(step + 1);
  };

  return (
    <div className="fixed inset-0 bg-black/60 z-50 flex justify-center items-center p-4">
      <div className="bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        {/* Header */}
        <div className="border-b p-4 flex justify-between items-center sticky top-0 bg-white">
          <h2 className="font-semibold text-lg">
            {step === 1 ? 'Select Date & Guests' : step === 2 ? 'Traveler Details' : step === 3 ? 'Confirm & Pay' : 'Booking Confirmed'}
          </h2>
          <button onClick={onClose} className="p-2 hover:bg-gray-100 rounded-full">✕</button>
        </div>

        <div className="p-6">
          {step === 1 && (
            <form onSubmit={nextStep} className="space-y-6">
              <div>
                <label className="block text-sm font-medium mb-2">Select Date</label>
                <input type="date" required className="w-full border rounded-lg p-3" value={date} onChange={(e) => setDate(e.target.value)} />
              </div>
              <div>
                <label className="block text-sm font-medium mb-2">Number of Guests</label>
                <select className="w-full border rounded-lg p-3" value={guests} onChange={(e) => setGuests(Number(e.target.value))}>
                  {[1,2,3,4,5,6].map(num => <option key={num} value={num}>{num} Guest{num > 1 ? 's' : ''}</option>)}
                </select>
              </div>
              <button type="submit" className="w-full bg-blue-600 text-white py-3 rounded-lg font-medium hover:bg-blue-700 transition">
                Continue • {experience.currency}{total}
              </button>
            </form>
          )}

          {step === 2 && (
            <form onSubmit={nextStep} className="space-y-4">
              <div className="grid grid-cols-2 gap-4">
                <div>
                  <label className="block text-sm font-medium mb-1">Full Name</label>
                  <input required className="w-full border rounded-lg p-3" placeholder="John Doe" value={details.name} onChange={(e) => setDetails({...details, name: e.target.value})} />
                </div>
                <div>
                  <label className="block text-sm font-medium mb-1">Nationality</label>
                  <input required className="w-full border rounded-lg p-3" placeholder="USA" value={details.nationality} onChange={(e) => setDetails({...details, nationality: e.target.value})} />
                </div>
              </div>
              <div>
                <label className="block text-sm font-medium mb-1">Email</label>
                <input type="email" required className="w-full border rounded-lg p-3" placeholder="john@example.com" value={details.email} onChange={(e) => setDetails({...details, email: e.target.value})} />
              </div>
              <div>
                <label className="block text-sm font-medium mb-1">Phone Number (Optional)</label>
                <input type="tel" className="w-full border rounded-lg p-3" placeholder="+1 234 567 8900" value={details.phone} onChange={(e) => setDetails({...details, phone: e.target.value})} />
              </div>
              <div>
                <label className="block text-sm font-medium mb-1">Special Requests or Dietary Requirements</label>
                <textarea className="w-full border rounded-lg p-3" rows="3" placeholder="e.g. Vegetarian, allergies..." value={details.requests} onChange={(e) => setDetails({...details, requests: e.target.value})} />
              </div>
              <div className="flex gap-3 pt-4">
                <button type="button" onClick={() => setStep(1)} className="px-6 py-3 border rounded-lg hover:bg-gray-50">Back</button>
                <button type="submit" className="flex-1 bg-blue-600 text-white py-3 rounded-lg font-medium hover:bg-blue-700">Continue to Payment</button>
              </div>
            </form>
          )}

          {step === 3 && (
            <div className="space-y-6">
              <div className="bg-gray-50 p-4 rounded-xl border">
                <h3 className="font-semibold mb-2">Order Summary</h3>
                <div className="flex justify-between py-2 border-b">
                  <span>{experience.title}</span>
                </div>
                <div className="flex justify-between py-2 text-sm text-gray-600">
                  <span>{date} • {guests} Guests</span>
                  <span>{guests} × {experience.currency}{experience.price}</span>
                </div>
                <div className="flex justify-between py-3 font-bold text-lg">
                  <span>Total (EUR)</span>
                  <span>{experience.currency}{total}</span>
                </div>
              </div>

              {/* Mock Razorpay UI */}
              <div className="border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                <div className="bg-[#02042b] text-white p-4 flex justify-between items-center">
                  <div>
                    <h3 className="font-bold text-lg">Razorpay</h3>
                    <p className="text-xs text-gray-300">WanderLocal Experiences</p>
                  </div>
                  <div className="text-right">
                    <div className="font-bold text-lg">{experience.currency}{total}</div>
                    <div className="text-xs text-gray-300">Test Mode</div>
                  </div>
                </div>
                <div className="p-5 bg-white">
                  <div className="flex justify-between items-center mb-4">
                    <h3 className="font-semibold text-gray-800">Card Details</h3>
                    <div className="flex gap-1 text-sm bg-gray-100 px-2 py-1 rounded">
                      <span className="text-blue-600 font-bold">VISA</span>
                      <span className="text-red-500 font-bold">MC</span>
                    </div>
                  </div>
                  <div className="space-y-4">
                    <div className="border rounded-md focus-within:ring-2 ring-blue-500 focus-within:border-blue-500 transition-all">
                      <input className="w-full p-3 text-sm font-mono outline-none bg-transparent" placeholder="Card Number (e.g. 4111 •••• •••• 1111)" />
                    </div>
                    <div className="grid grid-cols-2 gap-4">
                      <div className="border rounded-md focus-within:ring-2 ring-blue-500 focus-within:border-blue-500 transition-all">
                        <input className="w-full p-3 text-sm font-mono outline-none bg-transparent" placeholder="Expiry MM/YY" />
                      </div>
                      <div className="border rounded-md focus-within:ring-2 ring-blue-500 focus-within:border-blue-500 transition-all">
                        <input className="w-full p-3 text-sm font-mono outline-none bg-transparent" placeholder="CVV" type="password" maxLength="4" />
                      </div>
                    </div>
                    <div className="border rounded-md focus-within:ring-2 ring-blue-500 focus-within:border-blue-500 transition-all">
                      <input className="w-full p-3 text-sm outline-none bg-transparent" placeholder="Cardholder Name" />
                    </div>
                    <div className="flex items-center gap-2 mt-4 text-xs text-gray-500">
                      <input type="checkbox" id="save-card" className="rounded" />
                      <label htmlFor="save-card">Save card securely for future payments</label>
                    </div>
                  </div>
                </div>
              </div>

              <div className="flex gap-3">
                <button type="button" onClick={() => setStep(2)} className="px-6 py-3 border rounded-lg hover:bg-gray-50 font-medium">Back</button>
                <button onClick={() => setStep(4)} className="flex-1 bg-[#3399cc] text-white py-3 rounded-lg font-bold hover:bg-[#2b82ad] transition shadow-md shadow-blue-500/20">
                  Pay {experience.currency}{total}
                </button>
              </div>
            </div>
          )}

          {step === 4 && (
            <div className="text-center py-8 space-y-6">
              <div className="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto text-4xl">✓</div>
              <div>
                <h2 className="text-2xl font-bold mb-2">Booking Confirmed!</h2>
                <p className="text-gray-600">Your Booking ID is #WL-{Math.floor(Math.random()*90000) + 10000}</p>
              </div>
              <div className="bg-blue-50 text-blue-800 p-4 rounded-xl text-left text-sm mb-6">
                <p className="font-semibold mb-1">Host Contact Info:</p>
                <p>{experience.host.name} • +39 345 678 9012</p>
                <p className="mt-2">Meeting Point: {experience.meetingPoint}</p>
              </div>
              <div className="flex gap-3 justify-center">
                <button onClick={onClose} className="px-6 py-3 border rounded-lg hover:bg-gray-50 font-medium">Close</button>
                <button className="px-6 py-3 bg-gray-900 text-white rounded-lg hover:bg-black font-medium flex gap-2 items-center">
                  📅 Add to Calendar
                </button>
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}