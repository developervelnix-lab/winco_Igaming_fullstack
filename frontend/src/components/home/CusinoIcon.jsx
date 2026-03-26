import React from 'react';

function CasinoIcon() {
  return (
    <svg width="64" height="64" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
      <defs>
        <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" style={{ stopColor: 'rgb(255,215,0)', stopOpacity: 1 }} />
          <stop offset="100%" style={{ stopColor: 'rgb(204,153,0)', stopOpacity: 1 }} />
        </linearGradient>
      </defs>
      <g transform="translate(32,32)">
        <circle r="28" fill="url(#gradient)" />
        <path d="M0 -28A28 28 0 0 1 28 0L20 0A20 20 0 0 0 0 -20Z" fill="rgba(200, 0, 0, 0.7)" />
        <path d="M0 28A28 28 0 0 1 -28 0L-20 0A20 20 0 0 0 0 20Z" fill="rgba(50, 50, 50, 0.7)" />
        <path d="M25 10L28 15L20 18" stroke="white" strokeWidth="2" strokeLinecap="round" />
        <path d="-25 10L-28 15L-20 18" stroke="white" strokeWidth="2" strokeLinecap="round" />
      </g>
    </svg>
  );
}

export default CasinoIcon;