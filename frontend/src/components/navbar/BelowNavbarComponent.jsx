import React from 'react';

function BelowNavbarComponent() {
  const games = [
    "Cricket", "Football", "Live", "Avatar", "Tennis", "Basketball", "Hockey",
    "Rugby", "Esports", "MMA", "Boxing"
  ];

  return (
    <div className="w-full bg-white py-2">
      <div className="flex justify-center gap-5 items-center overflow-x-auto whitespace-nowrap">
        {games.map((game, index) => (
          <button
            key={index}
            className="bg-gradient-to-r from-gray-800 to-gray-900 text-black dark:text-white px-3 py-1 rounded-lg text-sm font-semibold shadow-md hover:from-gray-700 hover:to-gray-800 transition transform hover:scale-105 mx-2" // Added mx-2 for spacing
          >
            {game}
          </button>
        ))}
      </div>
    </div>
  );
}

export default BelowNavbarComponent;