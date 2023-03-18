$(function (){
    'use strict';
    let minValue = document.getElementById("min-value");
    let maxValue = document.getElementById("max-value");
    
    function validateRange(minPrice, maxPrice) {
      if (minPrice > maxPrice) {
    
        // Swap to Values
        let tempValue = maxPrice;
        maxPrice = minPrice;
        minPrice = tempValue;
      }
    
      minValue.innerHTML = minPrice + "SR";
      maxValue.innerHTML = maxPrice + "SR";
    }
    
    const inputElements = document.querySelectorAll("input[type=range]");
    
    inputElements.forEach((element) => {
      element.addEventListener("change", (e) => {
        let minPrice = parseInt(inputElements[0].value);
        let maxPrice = parseInt(inputElements[1].value);
    
        validateRange(minPrice, maxPrice);
      });
    });
    
    validateRange(parseInt(inputElements[0].value), parseInt(inputElements[1].value));

    
    $('.filter-container .hotel-class input[type=radio]').on('click', function () {
      let inputId = $(this).attr('id');
      if (inputId == 'one-star') {
        $('.filter-container .hotel-class i').each(function () {
          $(this).removeClass('golden')
        });
        $('.filter-container .hotel-class i.one-star').each(function () {
          $(this).addClass('golden')
        });
      } else if (inputId == 'two-star') {
        $('.filter-container .hotel-class i').each(function () {
          $(this).removeClass('golden')
        });
        $('.filter-container .hotel-class i.two-star').each(function () {
          $(this).addClass('golden')
        });
      } else if (inputId == 'three-star') {
        $('.filter-container .hotel-class i').each(function () {
          $(this).removeClass('golden')
        });
        $('.filter-container .hotel-class i.three-star').each(function () {
          $(this).addClass('golden')
        });
      } else if (inputId == 'four-star') {
        $('.filter-container .hotel-class i').each(function () {
          $(this).removeClass('golden')
        });
        $('.filter-container .hotel-class i.four-star').each(function () {
          $(this).addClass('golden')
        });
      } else if (inputId == 'five-star') {
        
        $('.filter-container .hotel-class i').each(function () {
          $(this).addClass('golden')
        });
      }

    });

    $('.filter-container .sort-by input').on('click', function () {
      $('.filter-container .sort-by label').removeClass('active');
      $(this).prev().addClass('active');
    });

    let checkedStar = $('.filter-container .hotel-class input[type=radio]:checked ').attr('id');
    if (checkedStar == 'one-star') {
      $('.filter-container .hotel-class i').each(function () {
        $(this).removeClass('golden')
      });
      $('.filter-container .hotel-class i.one-star').each(function () {
        $(this).addClass('golden')
      });
    } else if (checkedStar == 'two-star') {
      $('.filter-container .hotel-class i').each(function () {
        $(this).removeClass('golden')
      });
      $('.filter-container .hotel-class i.two-star').each(function () {
        $(this).addClass('golden')
      });
    } else if (checkedStar == 'three-star') {
      $('.filter-container .hotel-class i').each(function () {
        $(this).removeClass('golden')
      });
      $('.filter-container .hotel-class i.three-star').each(function () {
        $(this).addClass('golden')
      });
    } else if (checkedStar == 'four-star') {
      $('.filter-container .hotel-class i').each(function () {
        $(this).removeClass('golden')
      });
      $('.filter-container .hotel-class i.four-star').each(function () {
        $(this).addClass('golden')
      });
    } else if (checkedStar == 'five-star') {
      
      $('.filter-container .hotel-class i').each(function () {
        $(this).addClass('golden')
      });
    }

});