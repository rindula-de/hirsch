import { Controller } from '@hotwired/stimulus';
import {visit} from "@hotwired/turbo";

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static values = {
        participants: Array,
    }

    connect() {
        this.canvas = document.createElement('canvas');
        this.canvas.style.width = '100%';
        this.canvas.width = 800;
        this.canvas.height = 750;
        this.wheel_offset = 5;
        this.redrawTime = 1000/60;
        this.animationId = null;
        this.requested = true;
        this.radius = this.canvas.height / 2 - this.wheel_offset;
        this.element.appendChild(this.canvas);

        this.drawWheel();
    }

    drawWheel(angle = 0) {
        const segmentWidth = 360 / this.participantsValue.length;
        let endAngle = angle + segmentWidth;
        const ctx = this.canvas.getContext('2d');
        ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        ctx.beginPath();
        ctx.save();
        ctx.translate(this.canvas.width / 2 + this.radius + 50, this.canvas.height / 2);
        ctx.lineTo(0, -20);
        ctx.lineTo(0, 20);
        ctx.lineTo(-50, 0);
        ctx.fillStyle = '#C4B454';
        ctx.fill();
        ctx.stroke();
        ctx.restore();
        ctx.arc(this.canvas.width / 2, this.canvas.height / 2, this.radius, 0, 2 * Math.PI);
        ctx.stroke();

        for (let i = 0; i < this.participantsValue.length; i++) {
            ctx.beginPath();
            ctx.lineTo(this.canvas.width / 2, this.canvas.height / 2);
            ctx.arc(this.canvas.width / 2, this.canvas.height / 2, this.radius, angle * Math.PI / 180, endAngle * Math.PI / 180);
            ctx.lineTo(this.canvas.width / 2, this.canvas.height / 2);

            // generate hex value from string
            let hash = 0;
            for (let j = 0; j < this.participantsValue[i].length; j++) {
                hash = this.participantsValue[i].charCodeAt(j) + ((hash << 5) - hash);
            }
            let color = '#';
            for (let j = 0; j < 3; j++) {
                let value = (hash >> (j * 8)) & 0xFF;
                color += ('00' + value.toString(16)).slice(-2);
            }
            ctx.fillStyle = color;
            ctx.fill();
            ctx.save();

            // get luminance color to choose black or white text
            let luminance = 0.2126 * parseInt(color.slice(1, 3), 16) + 0.7152 * parseInt(color.slice(3, 5), 16) + 0.0722 * parseInt(color.slice(5, 7), 16);
            ctx.fillStyle = luminance > 128 ? '#000000' : '#ffffff';

            ctx.font = 'bold 20px sans-serif';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            if (this.participantsValue.length > 1) {
                // const angleVal = (i + 0.5) * segmentWidth * Math.PI / 180;
                const angleVal = (angle + (segmentWidth / 2)) * Math.PI / 180;
                ctx.translate(this.canvas.width / 2 + (this.radius * Math.cos(angleVal) / 2), this.canvas.height / 2 + (this.radius * Math.sin(angleVal) / 2));
                ctx.rotate((angle + (segmentWidth / 2)) * Math.PI / 180);
                ctx.fillText(this.participantsValue[i], 0, 0);
            } else {
                ctx.fillText(this.participantsValue[i], this.canvas.width / 2, this.canvas.height / 2);
            }
            ctx.restore();

            if (this.participantsValue.length !== 1) {
                ctx.stroke();
            }
            angle += segmentWidth;
            endAngle += segmentWidth;
        }
    }

    spin() {
        if (this.participantsValue.length <= 1) return;
        if (this.animationId) return;
        this.requested = false;

        const guessItemIndex = Math.floor(Math.random() * this.participantsValue.length);
        this.winner = this.participantsValue[guessItemIndex];

        this.totalTime = 0;
        this.maxAngle =
            360 * 20 +
            (this.participantsValue.length - 1 - guessItemIndex) * (360/this.participantsValue.length) +
            Math.random() * (360/this.participantsValue.length);
        this.endTime = (this.maxAngle / 50) * this.redrawTime;

        this.beginAnimation();
    }

    beginAnimation() {
        const angle = this.easeOut(this.totalTime, 0, this.maxAngle, this.endTime);

        if (this.totalTime < this.endTime) {
            console.log(this.totalTime, this.endTime, this.maxAngle, angle)
            setTimeout(() => {
                this.drawWheel(angle);
                this.animationId = requestAnimationFrame(this.beginAnimation.bind(this));
                this.totalTime += this.redrawTime;
                this.animationId = window.requestAnimationFrame(this.beginAnimation.bind(this));
            }, this.redrawTime);
        } else if (this.animationId !== null && !this.requested) {
            this.requested = true;
            window.cancelAnimationFrame(this.animationId);
            this.animationId = null;
            fetch('/api/spin-the-wheel', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    winner: this.winner,
                }),
            }).then(() => {
                // redirect to the same page using Turbo after 5 seconds
                setTimeout(() => {
                    visit(window.location.href);
                }, 5000);
            });
        }
    }

    easeOut(time, beginningVal, toChange, duration) {
        return time == duration
            ? beginningVal + toChange
            : toChange * (-Math.pow(2, (-10 * time) / duration) + 1) + beginningVal;
    }

}
