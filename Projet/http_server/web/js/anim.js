
 console.log("Hello World!");   
    
 let complexTransitionTime = 3000    // <-- for complex animation
    let transitionTime = 1000           // <-- 100 ms - time our animation will last
    let previousTime, start = 0;        // <-- stores data on animation
    let angle = 45;                    // <-- angle of gradient
    let animationDirection = 'forwards' // <-- stores the animation direction
    let complexAnDirection = 'forwards' // <-- for continuous animation
    let element = 'gradient-button-transition'; // <-- id of our button
    let intervalFrame;                          // <-- stores the interval frame
    let complexIntervalFrame;                   // <-- for 'always on' gradient transition
    let currentPct = 0;                         // <-- current percentage through the animation
    let complexCurrentPct = 0;                  // <-- current pct for complex animation
    let elapsed = 0;                            // <-- number of frames which have ellapsed 
    let complexElapsed = 0;                     // <-- complex elapsed time
    
    // GRADIENT COLORS
    const gradientStopOne = [
        { pct: 0,  color: { r: 255, g: 113, b: 71 } }, // The first color in your gradient
        { pct: 100, color: { r: 0, g: 55, b: 255 } }   // The color you want your first color to transition to
    ];
    const gradientStopTwo = [
        { pct: 0,  color: { r: 224, g: 65, b: 127 } }, // The second color in your gradient
        { pct: 100, color: { r: 0, g: 173, b: 255 } }  // The color you want your second color to transition to
    ]
    const complexGradientOne = [
        { pct: 0,  color: { r: 224, g: 65, b: 127 } }, 
        { pct: 33, color: { r: 0, g: 173, b: 255 } },
        { pct: 66, color: { r: 203, g: 252, b: 5 } },
        { pct: 100, color: { r: 98, g: 5, b: 252 } }
    ]
    const complexGradientTwo = [
        { pct: 0,  color: { r: 255, g: 113, b: 71 } },
        { pct: 33,  color: { r: 0, g: 55, b: 255 } },
        { pct: 66, color: { r:30, g: 177, b: 7 } },
        { pct: 100, color: { r:228, g: 44, b: 200 } }
    ]
   
    // This function transitions between two rgb colors
    const getColor = function(pct, colorSet) {
        for (var i = 1; i < colorSet.length - 1; i++) {
            if (pct < colorSet[i].pct) {
                break;
            }
        }
        // This conversion figures out the transition between two rgb values
        var lower = colorSet[i - 1];
        var upper = colorSet[i];
        var range = upper.pct - lower.pct;
        var rangePct = (pct - lower.pct) / range;
        var pctLower = 1 - rangePct;
        var pctUpper = rangePct;
        var color = {
            r: Math.floor(lower.color.r * pctLower + upper.color.r * pctUpper),
            g: Math.floor(lower.color.g * pctLower + upper.color.g * pctUpper),
            b: Math.floor(lower.color.b * pctLower + upper.color.b * pctUpper)
        };
        // And returns the rgb code
        return `rgb(${color.r}, ${color.g}, ${color.b})`;
    }

    // This is our animation which we run on hover
    const complexGradientAnimation = function() {
        if(complexIntervalFrame === undefined) {
            complexIntervalFrame = setInterval(() => {
                let time = complexTransitionTime / 1000;
                let numberOfFrames = time * 60; // 60 frames per second -> 1 second = 60 frames
                
                if(complexCurrentPct === 100) {
                    complexAnDirection = 'backwards';
                }
                else if(complexCurrentPct === 0) {
                    complexAnDirection = 'forwards';
                }
                // If the animation is going forward
                if(complexAnDirection == 'forwards') {
                    // Add 1 to elapsed
                    complexElapsed += 1;
                    // The elapsed frames out of max frames
                    complexCurrentPct = Math.min(complexElapsed / numberOfFrames, 1) * 100;
                }
                else if(complexAnDirection === 'backwards') {
                    // Otherwise we're going back - subtract 1 from ellapsed
                    complexElapsed -= 1;
                    // The elapsed frames out of max frames
                    complexCurrentPct = Math.max(complexElapsed / numberOfFrames, 0) * 100;
                }

                // Calculate current color in this time for each gradient color
                let colorOne = getColor(complexCurrentPct, complexGradientOne);
                let colorTwo = getColor(complexCurrentPct, complexGradientTwo);

                // Generate CSS string
                let generateGradient = `linear-gradient(${angle}deg, ${colorOne}, ${colorTwo})`;

                // Add it to our background.
                document.getElementById('complex-gradient-transition').style.backgroundImage = generateGradient;

            }, 16.667); // 60 frames per second
        }
    };
    
    complexGradientAnimation();
