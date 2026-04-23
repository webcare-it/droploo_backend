import { useRef, useState } from 'react';
import styled from 'styled-components';

const Image = styled.img.attrs((props) => ({
  src: props.source,
}))`
  width: 100%;
  height: auto;
`;

const Target = styled(Image)`
  position: absolute;
  left: ${(props) => props.offset.left}px;
  top: ${(props) => props.offset.top}px;
  transform: scale(2); /* Zoom effect */
  transform-origin: top left;
  pointer-events: none; /* Prevent interfering with mouse events */
  opacity: ${(props) => props.opacity};
  transition: opacity 0.2s ease-in-out;
  border: none;
`;

const Container = styled.div`
  position: relative;
  display: inline-block; /* To fit the image size */
  width: auto; /* Adjust dynamically to the image */
  height: auto;
  overflow: hidden;
`;

const MagnifyImage = ({ image }) => {
  const sourceRef = useRef(null);
  const containerRef = useRef(null);

  const [opacity, setOpacity] = useState(0);
  const [offset, setOffset] = useState({ left: 0, top: 0 });

  const handleMouseEnter = () => {
    setOpacity(1); // Show the zoomed image
  };

  const handleMouseLeave = () => {
    setOpacity(0); // Hide the zoomed image
  };

  const handleMouseMove = (e) => {
    const containerRect = containerRef.current.getBoundingClientRect();
    const sourceRect = sourceRef.current.getBoundingClientRect();

    const xRatio =
      (sourceRect.width * 2 - containerRect.width) / sourceRect.width;
    const yRatio =
      (sourceRect.height * 2 - containerRect.height) / sourceRect.height;

    // Get the cursor position relative to the container
    const left = e.pageX - containerRect.left;
    const top = e.pageY - containerRect.top;

    // Calculate the offset to center the zoom on the cursor
    let offsetX = left * xRatio;
    let offsetY = top * yRatio;

    // Prevent the zoomed image from going out of bounds at the bottom
    if (offsetY > sourceRect.height) {
      offsetY = sourceRect.height;
    }
    if (offsetX > sourceRect.width) {
      offsetX = sourceRect.width;
    }

    setOffset({ left: -offsetX, top: -offsetY }); // Adjust zoomed image position
  };

  return (
    <div className="App">
      <Container
        ref={containerRef}
        onMouseEnter={handleMouseEnter}
        onMouseLeave={handleMouseLeave}
        onMouseMove={handleMouseMove}
      >
        {/* Source image */}
        <Image ref={sourceRef} alt="source" source={image} />

        {/* Target image (Zoomed) */}
        <Target alt="target" opacity={opacity} offset={offset} source={image} />
      </Container>
    </div>
  );
};

export default MagnifyImage;
