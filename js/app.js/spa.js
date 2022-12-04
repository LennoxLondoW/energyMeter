
function loadData(load_to, load_link, relocating_to = false) {
  try {
    before_spa();
  } catch (error) {
    console.log('Before spa is needed');
  }

  let h1 = load_to.prop('scrollHeight');
  let h2 = $(window).height();
  let h = (h1 > h2 ? h2 : h1) + 'px';
  load_to.html(`<div style="width:100%; height:` + h + `; display:flex; align-items:center; justify-content:center;">
        <svg xmlns:svg="http://www.w3.org/2000/svg"
            xmlns="http://www.w3.org/2000/svg"
            xmlns:xlink="http://www.w3.org/1999/xlink" version="1.0" width="160px" height="20px" viewBox="0 0 128 16" xml:space="preserve">
            <path fill="#949494" d="M6.4,4.8A3.2,3.2,0,1,1,3.2,8,3.2,3.2,0,0,1,6.4,4.8Zm12.8,0A3.2,3.2,0,1,1,16,8,3.2,3.2,0,0,1,19.2,4.8ZM32,4.8A3.2,3.2,0,1,1,28.8,8,3.2,3.2,0,0,1,32,4.8Zm12.8,0A3.2,3.2,0,1,1,41.6,8,3.2,3.2,0,0,1,44.8,4.8Zm12.8,0A3.2,3.2,0,1,1,54.4,8,3.2,3.2,0,0,1,57.6,4.8Zm12.8,0A3.2,3.2,0,1,1,67.2,8,3.2,3.2,0,0,1,70.4,4.8Zm12.8,0A3.2,3.2,0,1,1,80,8,3.2,3.2,0,0,1,83.2,4.8ZM96,4.8A3.2,3.2,0,1,1,92.8,8,3.2,3.2,0,0,1,96,4.8Zm12.8,0A3.2,3.2,0,1,1,105.6,8,3.2,3.2,0,0,1,108.8,4.8Zm12.8,0A3.2,3.2,0,1,1,118.4,8,3.2,3.2,0,0,1,121.6,4.8Z"/>
            <g>
                <path fill="#000000" d="M-42.7,3.84A4.16,4.16,0,0,1-38.54,8a4.16,4.16,0,0,1-4.16,4.16A4.16,4.16,0,0,1-46.86,8,4.16,4.16,0,0,1-42.7,3.84Zm12.8-.64A4.8,4.8,0,0,1-25.1,8a4.8,4.8,0,0,1-4.8,4.8A4.8,4.8,0,0,1-34.7,8,4.8,4.8,0,0,1-29.9,3.2Zm12.8-.64A5.44,5.44,0,0,1-11.66,8a5.44,5.44,0,0,1-5.44,5.44A5.44,5.44,0,0,1-22.54,8,5.44,5.44,0,0,1-17.1,2.56Z"/>
                <animateTransform attributeName="transform" type="translate" values="23 0;36 0;49 0;62 0;74.5 0;87.5 0;100 0;113 0;125.5 0;138.5 0;151.5 0;164.5 0;178 0" calcMode="discrete" dur="260ms" repeatCount="indefinite"/>
            </g>
        </svg>
      </div>`).css({
    'transition': 'all 1s !important',
    'height': h,
  });

  if (relocating_to) {
    $('html,body').animate({
      scrollTop: $(relocating_to).offset().top - 100
    }, 500);
  }

  //lets fetch content  field.load(url, {limit: 25}, function (responseText, textStatus, XMLHttpRequest) 
  load_to.load(load_link, {
    limit: 25
  }, function (responseText, textStatus, XMLHttpRequest) {


    if (textStatus == "error") {
      load_to.html(`<div style="width:100%; height:` + h + `; display:flex; align-items:center; justify-content:center;"><svg version="1.1" id="Layer_1"
	xmlns="http://www.w3.org/2000/svg"
	xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 512.001 512.001" style="enable-background:new 0 0 512.001 512.001; width: 100px; height:100px; object-fit:cover;" xml:space="preserve">
	<path style="fill:#4FC0E8;" d="M512.001,256.006c0,141.395-114.606,255.998-255.996,255.994
	C114.606,512.004,0.001,397.402,0.001,256.006C-0.007,114.61,114.606,0,256.005,0C397.395,0,512.001,114.614,512.001,256.006z"/>
	<path style="fill:#3DAED9;" d="M498.97,336.633L257.308,94.97c-0.001-0.001-0.001-0.001-0.001-0.001l-3.335-3.335
	c-0.634-0.636-1.393-1.142-2.235-1.494c-0.838-0.35-1.744-0.539-2.67-0.539H96.534c-3.829,0-6.933,3.104-6.933,6.933v291.2
	c0,2.347,1.243,4.32,3.027,5.574c0.462,0.658,112.783,112.978,113.441,113.441c0.117,0.167,0.297,0.272,0.426,0.426
	c16.022,3.14,32.567,4.828,49.511,4.827C369.214,512.003,465.185,438.5,498.97,336.633z"/>
	<g>
		<path style="fill:#F4F6F9;" d="M249.067,172.8h62.4v48.533h13.867v-55.467c0-0.926-0.189-1.831-0.539-2.67
		c-0.352-0.842-0.859-1.6-1.494-2.235l-69.329-69.329c-0.635-0.635-1.393-1.142-2.235-1.494c-0.838-0.35-1.744-0.539-2.67-0.539
		H96.534c-3.829,0-6.933,3.104-6.933,6.933v291.2c0,3.829,3.104,6.933,6.933,6.933h138.667V380.8H103.467V103.467h138.667v62.4
		C242.134,169.696,245.238,172.8,249.067,172.8z M256.001,113.27l45.662,45.663h-45.662V113.27z"/>
		<path style="fill:#F4F6F9;" d="M421.669,412.366l-83.2-166.4c-2.35-4.699-10.054-4.699-12.404,0l-83.2,166.4
		c-1.073,2.149-0.958,4.702,0.305,6.747c1.263,2.042,3.493,3.287,5.898,3.287h166.4c2.403,0,4.635-1.246,5.898-3.287
		C422.627,417.068,422.742,414.515,421.669,412.366z M260.286,408.533l71.981-143.965l71.981,143.965H260.286z"/>
		<path style="fill:#F4F6F9;" d="M325.334,317.14v45.378c0,3.829,3.104,6.933,6.933,6.933c3.829,0,6.933-3.104,6.933-6.933V317.14
		c0-3.829-3.104-6.933-6.933-6.933C328.438,310.207,325.334,313.312,325.334,317.14z"/>
		<path style="fill:#F4F6F9;" d="M332.267,378.275c-3.83,0-6.933,3.116-6.933,6.94c0,3.836,3.103,6.927,6.933,6.927
		c3.837,0,6.933-3.091,6.933-6.927C339.201,381.391,336.104,378.275,332.267,378.275z"/>
	</g>
  </svg></div>
  `);
    } else {
      try {
        showImages();
        loadDynamic();
        after_spa();
        $('title').html($("#page_title_holder").val());
        $('meta[name="description"]').attr("content",$("#page_description_holder").val());
        $('link[rel="icon"]').attr("href",$("#page_icon_holder").val());
      } catch (error) {
        console.log(error);
      }
      setTimeout(() => {
        load_to.css({
          'transition': 'all .5s',
          'height': load_to.prop('scrollHeight') + 'px',
        });

        load_to.parents().css({
          'transition': 'all .5s',
          'height': 'auto',
        });
      }, 50);

    }
  });




}

function loadDynamic() {
  const targets = document.querySelectorAll("[data-dynamic]");
  const lazyLoad = target => {
    const io = new IntersectionObserver((entries, observer) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const div = entry.target;
          const url = div.getAttribute('data-dynamic');
          div.removeAttribute('data-dynamic');
          loadData(
            load_to = $(div),
            load_link = url,
            false
          );
          observer.disconnect();
        }
      })
    }, {
      threshold: [0]
    });
    io.observe(target);
  }
  targets.forEach(lazyLoad);
}




$(function () {
  showImages();
  loadDynamic();
  $("body").on("click", 'a', function (event) {
    if($(this).hasClass('ignore')){
      return;
    }
    var href = $(this).attr("href"),
      replaced = href.replace(/ /g, "");
    if (replaced.substr(0,1) === "#" || replaced === 'javascript:void(0)') {
      return false;
    }
    if (!$(this).hasClass("non_spa") && replaced.substr(0, 1) !== "#" && $('[data-editable]:first').attr('data-editable') === undefined) {
      event.preventDefault();
      window.history.pushState('view', 'view', href);
      var to = $(this).attr("data-load_to");
      var id = to === undefined ? "#main_field" : to;
      loadData(
        load_to = $(id),
        load_link = href,
        relocating_to = id);
      return false;
    }

  });


  //searching
  $("body").on("submit", "#search_users", function (event) {
    event.preventDefault();
    $("#search_user").trigger("blur");
    var href = $("#base_path").val() + "view_users/search/" + encodeURI($("#search_user").val());
    window.history.pushState('view', 'view', href);
    loadData(
      load_to = $("#main_field"),
      load_link = href,
      relocating_to = "#main_field");
    return false;
  });

})



if (window.history && window.history.pushState) {
  $(window).on('popstate', function () {
    // $('#stream_div').fadeOut(100).find('iframe').removeAttr('src'); 
    var hashLocation = location.hash;
    var hashSplit = hashLocation.split("#!/");
    var hashName = hashSplit[1];

    if (hashName !== '') {
      var hash = window.location.hash;
      if (hash === '') {
        loadData(
          load_to = $("#main_field"),
          load_link = window.location.href,
          relocating_to = "#main_field");
      }
    }
  });

}


function showImages() {
  var targets = document.querySelectorAll("svg[data-src]");

  var lazyLoad = target => {
    var io = new IntersectionObserver((entries, observer) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          var img = entry.target;
          var src = img.getAttribute("data-src");
          var alt = img.getAttribute("data-alt");
          img.removeAttribute("data-src");
          if (src !== null && src !== '') {
            $(img).parent().append(`
                               <img style='display:none;' src='`+ src + `' alt='` + alt + `' onload='$(this).parent().children().fadeToggle(100)'>
                            `);
            img.setAttribute("src", src);
          }
          // img.removeAttribute('data-src')
          img.style.visibility = 'visible';
          observer.disconnect();
        }
      })
    }, { threshold: [0] });
    io.observe(target);
  }
  targets.forEach(lazyLoad);
}