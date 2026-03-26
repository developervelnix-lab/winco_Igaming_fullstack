/*
  Author: DevKilla
  Buy Code From: jinkteam.com
  Contact: @devkilla (Telegram)
*/

import React, { useState } from 'react';

const OpenBets = () => {
  const [isCollapsed, setIsCollapsed] = useState(false);

  const toggleCollapse = () => {
    setIsCollapsed(!isCollapsed);
  };

  return (
    <div className="w-full max-w-4xl mx-auto bg-gray-100 rounded-md overflow-hidden">
      <div 
        className="bg-pink-700 text-black dark:text-white p-2 flex justify-between items-center cursor-pointer"
        onClick={toggleCollapse}
      >
        <h2 className="text-xl font-medium">Matched Bets</h2>
        <svg 
          className={`w-6 h-6 transition-transform ${isCollapsed ? 'rotate-180' : ''}`}
          fill="none" 
          stroke="currentColor" 
          viewBox="0 0 24 24"
        >
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 15l7-7 7 7" />
        </svg>
      </div>
      
      {!isCollapsed && (
        <div className="p-6 bg-white">
          <p className="text-lg text-gray-800">You have no Matched Bets.</p>
        </div>
      )}
    </div>
  );
};

export default OpenBets;