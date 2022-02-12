import { Controller } from "stimulus";
import { TweenLite, TweenMax, TimelineMax, Power3, Back } from "gsap/gsap-core";

export default class extends Controller {
    static targets = ['icon', 'svg']
    connect() {
        this.iconTarget.innerHTML = '<svg data-navicon-target="svg" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"  viewBox="0 0 600 600"><defs></defs><path id="meat" fill="none" d="M348,305.5h-68.8c-2.7,0-4.9,2.2-4.9,4.9c0,2.7,2.2,4.9,4.9,4.9H348c2.7,0,4.9-2.2,4.9-4.9C352.9,307.7,350.7,305.5,348,305.5z" /><path id="topBun" fill="none" d="M323.4,276h-19.7c-13.6,0-24.6,11-24.6,24.6H348C348,287,337,276,323.4,276z" /><path id="bottomBun" fill="none" d="M279.2,330.1c0,2.7,2.2,4.9,4.9,4.9h59c2.7,0,4.9-2.2,4.9-4.9v-9.8h-68.8V330.1z" /><path id="bottom" fill="#EDEDED" d="M334.8,320.8h-42.5c-2.1,0-3.8,1.7-3.8,3.8c0,2.1,1.7,3.8,3.8,3.8h42.5c2.1,0,3.8-1.7,3.8-3.8C338.7,322.5,337,320.8,334.8,320.8z" /><path id="middle" fill="#EDEDED" d="M334.8,307.6h-42.5c-2.1,0-3.8,1.7-3.8,3.8c0,2.1,1.7,3.8,3.8,3.8h42.5c2.1,0,3.8-1.7,3.8-3.8C338.7,309.3,337,307.6,334.8,307.6z" /><path id="top" fill="#EDEDED" d="M334.8,294.4h-42.5c-2.1,0-3.8,1.7-3.8,3.8s1.7,3.8,3.8,3.8h42.5c2.1,0,3.8-1.7,3.8-3.8S337,294.4,334.8,294.4z" /><g id="seedGroup"><path d="M301.3,296.7c-1.4,0-2.5-1.1-2.5-2.5c0-1.4,1.1-2.5,2.5-2.5s2.5,1.1,2.5,2.5C303.7,295.6,302.6,296.7,301.3,296.7z" /><path d="M325.9,291.8c-1.4,0-2.5-1.1-2.5-2.5c0-1.4,1.1-2.5,2.5-2.5c1.4,0,2.5,1.1,2.5,2.5C328.3,290.7,327.2,291.8,325.9,291.8z" /><path d="M311.1,286.8c-1.4,0-2.5-1.1-2.5-2.5c0-1.4,1.1-2.5,2.5-2.5s2.5,1.1,2.5,2.5C313.6,285.7,312.5,286.8,311.1,286.8z" /></g><path id="cheese" fill="#2d2d2d" d="M342.2,305l-16,5.4l-16.7-5.4H342.2z" /><rect id="hit" width="70" height="70" x="280" y="280" fill="rgba(0,0,0,0)" /></svg>';
        this.iconTarget.addEventListener("click", function () {
            let x = document.getElementById("navbar");
            if (x.className === "navbar") {
                x.className += " responsive";
            } else {
                x.className = "navbar";
            }
        })

        ////////////////////////////////////////////////////////////////////////////////
        // Tween Code
        ////////////////////////////////////////////////////////////////////////////////

        var xmlns = "http://www.w3.org/2000/svg",
            hit = this.svgTarget,
            isDevice,
            interactionUp,
            interactionDown, interactionOut, interactionOver, interactionMove;

        isDevice = (/android|webos|iphone|ipad|ipod|blackberry/i.test(navigator.userAgent.toLowerCase()));

        if (isDevice) {

            interactionUp = "touchend";
            interactionDown = "touchstart";
            interactionOut = interactionUp;
            interactionOver = interactionDown;
            interactionMove = 'touchmove';

        } else {

            interactionUp = "mouseup";
            interactionDown = "mousedown";
            interactionOut = "mouseout";
            interactionOver = "mouseover";
            interactionMove = 'mousemove';

        };
        if (isDevice) {
            hit.addEventListener(interactionUp, deviceBurger)


        } else {
            hit.addEventListener(interactionOver, function () {
                tl.play();
            })
            hit.addEventListener(interactionOut, function (e) {
                tl.reverse();
            })

        }


        function deviceBurger(e) {

            if (tl.time() > 0) {
                tl.reverse();
            } else {
                tl.play(0)
            }
        }

        TweenMax.set(['#seedGroup', '#cheese'], {
            fill: '#B62125'
        })
        TweenMax.set('svg', {
            visibility: 'visible',
            transformOrigin: '50% 50%',
            scale: 2
        })

        //TweenLite.defaultEase = Elastic.easeInOut.config(0.3, 0.84)
        TweenLite.defaultEase = Power3.easeInOut;
        var tl = new TimelineMax({
            paused: true
        });
        tl.to('#bottom', 2, {
            morphSVG: {
                shape: '#bottomBun'
            }
        })
            .to('#middle', 2, {
                morphSVG: {
                    shape: '#meat'
                }
            }, '-=1.8')
            .to('#top', 2, {
                morphSVG: {
                    shape: '#topBun'
                }
            }, '-=1.8')
            .staggerFrom('#seedGroup path', 0.41, {
                //y:-60,,
                transformOrigin: '50% 50%',
                scale: 0,
                ease: Back.easeOut
            }, 0.1, '-=1')
            .from('#cheese', 1, {
                scaleY: 0,
                x: -22,
                ease: Back.easeOut,
                transformOrigin: '50% 0%'
            }, '-=1')

        tl.timeScale(2.3);

    }
}