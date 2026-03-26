import React from 'react';
import { Carousel } from 'flowbite-react';
import { useColors } from '../../hooks/useColors';
import { URL as BASE_URL } from "../../utils/constants"

function FrontScrollableCard({ banners = [] }) {
  const COLORS = useColors();

  if (!banners || banners.length === 0) {
    return null;
  }

  return (
    <div className="w-full relative" style={{ backgroundColor: COLORS.bg }}>
      <Carousel
        className="h-[150px] sm:h-[200px] md:h-[280px] lg:h-[350px] xl:h-[410px]"
        indicators={true}
        leftControl={<span className="hidden group-hover:flex items-center justify-center w-10 h-10 md:w-14 md:h-14 rounded-full bg-black/10 dark:bg-black/60 text-black dark:text-white hover:bg-[#FFB000] hover:text-black transition-all duration-300 border border-black/10 dark:border-white/10 backdrop-blur-md text-lg md:text-2xl">←</span>}
        rightControl={<span className="hidden group-hover:flex items-center justify-center w-10 h-10 md:w-14 md:h-14 rounded-full bg-black/10 dark:bg-black/60 text-black dark:text-white hover:bg-[#FFB000] hover:text-black transition-all duration-300 border border-black/10 dark:border-white/10 backdrop-blur-md text-lg md:text-2xl">→</span>}
      >
        {banners.map((slide, index) => (
          <div key={index} className="relative w-full h-full overflow-hidden group">
            <img
              className="w-full h-full object-cover object-center scale-100 group-hover:scale-105 transition-transform duration-[6000ms] ease-out"
              src={`${BASE_URL}${encodeURI(slide.image_path)}`}
              alt={`Banner ${index + 1}`}
            />
          </div>
        ))}
      </Carousel>

      <style dangerouslySetInnerHTML={{ __html: `
        /* Flowbite Carousel Customization */
        .h-full > div {
          background-color: transparent !important;
        }
      ` }} />
    </div>
  );
}

export default FrontScrollableCard;