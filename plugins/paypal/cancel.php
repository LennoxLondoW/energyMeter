<!DOCTYPE html>
<html>

<head>
  <title></title>
  <style type="text/css">
    html,
    body {
      font-family: "Arial", monospace !important;
      height: 100%;
      padding-top: 0vh;
      margin: 0px;
      text-spacing: 1px;
      line-height: 30px
    }

    .container {
      height: auto;
      width: 100%;
      justify-content: center;
      align-item: center;
      display: flex;
      padding-top: 0px;
      padding-bottom: 0px
    }

    .text {
      font-weight: bold;
      font-size: 28px;
      color: #303030;
      padding-bottom: 50px
    }

    .dud {
      color: #000000;
    }

    .white-bg {
      width: 100%;
      background: #fff;
      padding-top: 100px
    }

    .heart {
      display: block;
      width: 100%;
      text-align: center;
    }

    .swing {
      font-style: normal;
      font-size: 64px;
      color: #55ff33;
      display: block;
      width: 75px;
      height: 55px;
      margin: 20px auto
    }

    .red-green {
      color: #55ff33 !important
    }

    .red-redcheck {
      color: #f50 !important
    }

    .message {
      display: block;
      margin: 0px auto 50px auto;
      border-radius: 15px;
      max-width: 600px;
      width: 90%;
      padding: 40px;
      text-align: left;
    }

    .message .info {
      font-style: normal;
      font-size: 18px;
      color: #757575;
      text-shadow:
    }

    .message img {
      border-radius: 10px;
      margin-top: 20px;
      box-shadow: 0px 30px 30px rgba(0, 50, 0, 0.1);
      border: 1px solid #fff
    }

    .message span {
      padding: 0px 25px 0px 25px;
      text-align: center;
      display: block;
      font-family: "Arial", monospace !important;
    }

    .message span strong {
      color: #ff7700
    }

    @keyframes fadeInUp {
      0% {
        transform: translateY(100%);
        opacity: 0
      }

      20% {
        transform: translateY(90%);
      }

      20% {
        transform: translateY(10%);
      }

      100% {
        transform: translateY(0%);
        opacity: 1
      }
    }


    .starterPosition {
      animation: 3s ease-in-out 0 1 fadeInUp;
    }

    .fadeBg {
      background: #f7faf7;
    }

    @keyframes fadeInUp {
      0% {
        transform: translateY(100%);
        opacity: 0
      }

      100% {
        transform: translateY(0%);
        opacity: 1
      }
    }


    .starterPosition {
      animation: 3s ease-in-out 0 1 fadeInUp;
    }
  </style>
</head>

<body class="fadeBg">

  <div class="white-bg">

    <div class="heart starterPosition enderPosition">
      <span class="swing">
        <em class="green-check">&#33;</em>
    </div>

    <div class="container starterPosition">
      <div class="text"></div>
    </div>
  </div>

  <div class="message starterPosition">
    <div class="info">
      <img class="starterPosition" src="p.png" width="100%" />
      <br><br>
      <span class="starterPosition">
        Transaction cancelled
      </span>
    </div>
  </div>


  <script>
    // ——————————————————————————————————————————————————
    // TextScramble
    // ——————————————————————————————————————————————————

    class TextScramble {
      constructor(el) {
        this.el = el
        this.chars = '!<>-_\\/[]{}—=+*^?#________'
        this.update = this.update.bind(this)
      }
      setText(newText) {
        const oldText = this.el.innerText
        const length = Math.max(oldText.length, newText.length)
        const promise = new Promise((resolve) => this.resolve = resolve)
        this.queue = []
        for (let i = 0; i < length; i++) {
          const from = oldText[i] || ''
          const to = newText[i] || ''
          const start = Math.floor(Math.random() * 40)
          const end = start + Math.floor(Math.random() * 40)
          this.queue.push({
            from,
            to,
            start,
            end
          })
        }
        cancelAnimationFrame(this.frameRequest)
        this.frame = 0
        this.update()
        return promise
      }
      update() {
        let output = ''
        let complete = 0
        for (let i = 0, n = this.queue.length; i < n; i++) {
          let {
            from,
            to,
            start,
            end,
            char
          } = this.queue[i]
          if (this.frame >= end) {
            complete++
            output += to
          } else if (this.frame >= start) {
            if (!char || Math.random() < 0.28) {
              char = this.randomChar()
              this.queue[i].char = char
            }
            output += `<span class="dud">${char}</span>`
          } else {
            output += from
          }
        }
        this.el.innerHTML = output
        if (complete === this.queue.length) {
          this.resolve()
        } else {
          this.frameRequest = requestAnimationFrame(this.update)
          this.frame++
        }
      }
      randomChar() {
        return this.chars[Math.floor(Math.random() * this.chars.length)]
      }
    }

    // ——————————————————————————————————————————————————
    // Example
    // ——————————————————————————————————————————————————

    const phrases = [
      'You cancelled the transaction',
    ]

    const el = document.querySelector('.text')
    const fx = new TextScramble(el)

    let counter = 0
    const next = () => {
      fx.setText(phrases[counter]).then(() => {
        setTimeout(next, 200000)
      })
      counter = (counter + 1) % phrases.length
    }

    next();
  </script>
</body>

</html>

