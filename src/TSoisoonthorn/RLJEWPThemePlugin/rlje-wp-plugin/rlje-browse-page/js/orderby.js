(function($){
    // Browse Page - Order by Date Added
    $('.js-orderby-added').click(function(e){
        e.preventDefault();
        activateOrder(this, 'added');
    });

    // Browse Page - Order by A to Z
    $('.js-orderby-az').click(function(e){
        e.preventDefault();
        activateOrder(this, 'az');
    });

    function activateOrder(elm, orderBy) {
        if(!$(elm).hasClass('active')) {
            $(elm).parent().find('a').removeClass('active');
            $(elm).addClass('active');
            sortBrowseItems(orderBy);
        }
    }

    // Sorting items in browse page
    function sortBrowseItems(type) {
        var dataValue = (typeof type === 'undefined') ? 'added': type,
            sortByData = 'data-'+dataValue,
            $items = $('.objects > .item > .row > div'),
            currentWidth = $($items[0]).css('width'),
            promises = [],
            positions = [],
            itemsSorted = $items.toArray().sort(function(a, b) {
                var result = 0;
                if(type === 'added') {
                    result = $(a).attr(sortByData) - $(b).attr(sortByData);
                }
                else {
                    if($(a).attr(sortByData) < $(b).attr(sortByData)) result = -1;
                    if($(a).attr(sortByData) > $(b).attr(sortByData)) result = 1;
                }
                return result;
            });

        $items.each(function() {
          //store original positions
          positions.push($(this).position());
        }).each(function(originalIndex) {
          //change items to absolute position
          var $this = $(this),
              newIndex = itemsSorted.indexOf(this);
          itemsSorted[newIndex] = $this.clone(); //copy the original item position
          $this.css("position", "absolute")
               .css("top", positions[originalIndex].top + "px")
               .css("left", positions[originalIndex].left + "px")

          //animating new positioning
          var promise = $this.animate({
            top: positions[newIndex].top + "px",
            left: positions[newIndex].left + "px",
            width: currentWidth
          }, 0);
          promises.push(promise);
    });

    //Replace each item with the new item ordered
    $.when.apply($, promises).done(function() {
        $items.each(function(index) {
          $(this).replaceWith(itemsSorted[index]);
        });
      });
    }

})(jQuery);
