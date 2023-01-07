import React from 'react'

const StartMessage = () => {
  return (
    <>
        <div id="g-container"></div>
        <div id="g-shadow">
            <div className="g-background g-upper"></div>
            <div className="g-background g-lower"></div>
        </div>

        <div id="g-message">
            <div className="g-background g-upper"></div>
            <div className="g-background g-lower"></div>
        <span id="g-text"></span>
        </div>
    </>
  );
}

export default StartMessage;