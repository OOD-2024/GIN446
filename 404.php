<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="shortcut icon" href="public/favicon.png" type="image/x-icon">

  <title>Page not Found</title>
  <style>
    body {
      font-family: "Roboto", sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f5f5f5;
    }

    .card.not-found {
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      padding: 2rem;
      padding-top: 0;
      max-width: 1000px;
      margin: 3rem auto;
      margin-top: 0;
    }

    .display-1 {
      font-size: 3.5rem;
      font-weight: 700;
      /* margin-bottom: 1.5rem; */
      margin: 0;
    }

    .display-3 {
      font-size: 2.5rem;
      font-weight: 600;
      margin-bottom: 1.5rem;
    }

    .paragraph-2 {
      font-size: 1.125rem;
      line-height: 1.6;
      color: #555;
      margin-bottom: 2rem;
    }

    .title-accent.v3 {
      color: #00b8d4;
    }

    .image-wrapper {
      text-align: center;
      margin-bottom: 2rem;
    }

    .image.cover {
      max-width: 100%;
      height: auto;
    }

    .btn-primary {
      display: inline-flex;
      align-items: center;
      background-color: #00b8d4;
      color: #fff;
      text-decoration: none;
      padding: 0.75rem 1.5rem;
      border-radius: 4px;
      transition: background-color 0.3s ease;
    }

    .btn-primary:hover {
      background-color: #00a0b4;
    }

    .line-rounded-icon.btn-primary-arrow {
      margin-left: 0.5rem;
      font-size: 1.2rem;
    }

    .mg-bottom-42px {
      margin-bottom: 42px;
    }

    .mg-bottom-48px {
      margin-bottom: 48px;
    }

    .center {
      text-align: center;
    }

    .buttons-row {
      display: flex;
      justify-content: center;
      gap: 1rem;
    }

    @media (max-width: 767px) {
      .card.not-found {
        padding: 1.5rem;
      }

      .display-1 {
        font-size: 2.5rem;
      }

      .display-3 {
        font-size: 2rem;
      }

      .paragraph-2 {
        font-size: 1rem;
      }

      .buttons-row {
        flex-direction: column;
      }
    }
  </style>
</head>

<body>
  <div class="card not-found">
    <div class="utility-page-content w-form">
      <h1
        style="
            transform: translate3d(0px, 0px, 0px) scale3d(1, 1, 1) rotateX(0deg)
              rotateY(0deg) rotateZ(0deg) skew(0deg, 0deg);
            opacity: 1;
            transform-style: preserve-3d;
          "
        class="display-1">
        Venn of a <span class="title-accent v3">404</span>
      </h1>
      <div class="mg-bottom-42px">
        <div class="inner-container not-found-main-image">
          <div class="image-wrapper">
            <img
              src="https://cdn.prod.website-files.com/66f568bcf43078836f8a9bf8/6729846f7f6587d3dc48c0dd_404.png"
              loading="eager"
              style="
                  transform: translate3d(0px, 0px, 0px) scale3d(1, 1, 1)
                    rotateX(0deg) rotateY(0deg) rotateZ(0deg) skew(0deg, 0deg);
                  opacity: 1;
                  transform-style: preserve-3d;
                "
              alt="Venn diagram of a 404 page"
              class="image cover" />
          </div>
        </div>
      </div>
      <div class="inner-container _400px---mbl center _100---mbp">
        <h2
          style="
              transform: translate3d(0px, 0px, 0px) scale3d(1, 1, 1)
                rotateX(0deg) rotateY(0deg) rotateZ(0deg) skew(0deg, 0deg);
              opacity: 1;
              transform-style: preserve-3d;
            "
          class="display-3 mg-bottom-48px">
          The Venn Diagram
        </h2>
        <p class="paragraph-2">
          Venn diagrams or set diagrams are diagrams that show all
          hypothetically possible logical relations between a finite
          collection of sets (groups of things). Venn diagrams were conceived
          around 1880 by John Venn. They are used in many fields, including
          set theory, probability, logic, statistics, computer science, and
          trying to visit web pages that don't exist.
        </p>
        <div
          style="
              transform: translate3d(0px, 0px, 0px) scale3d(1, 1, 1)
                rotateX(0deg) rotateY(0deg) rotateZ(0deg) skew(0deg, 0deg);
              opacity: 1;
              transform-style: preserve-3d;
            "
          class="buttons-row center">
          <a href="/" class="btn-primary w-button">Go back home </a>
        </div>
      </div>
    </div>
  </div>
</body>

</html>