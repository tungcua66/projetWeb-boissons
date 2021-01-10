$(document).ready(function() {
  $(document).on("click", ".menu", function(e) {
    var filterIngredient = e.target.getAttribute("value");
    $.ajax({
      type: "POST",
      url: "getRecipesContainingIngredient.php",
      data: { "filter_ing": filterIngredient },
      success: function(array) {
        var parsedData = jQuery.parseJSON(array);
        filterKeepRecipes(parsedData);
      }
    });
  });

  $(document).on("click", "#clearFilters", function(e) {
    location.reload();
  });


  $(document).on("click", ".addRecipeToBasket", function(e) {
    var recipeTitle = e.currentTarget.closest(".recipeTitle").children[0].getAttribute("value");
    $.ajax({
      type: "POST",
      url: "addOrRemoveRecipeBasket.php",
      data: { "rcp_name": recipeTitle, "add_recipe": "true" },
      success: function(array) {
        alert(recipeTitle);
      }
    });
  });
  $(document).on("click", ".removeRecipeFromBasket", function(e) {
    var user_id = e.target.getAttribute("value");
    var recipeTitle = e.currentTarget.closest(".recipeTitle").children[0].getAttribute("value");
    $.ajax({
      type: "POST",
      url: "addOrRemoveRecipeBasket.php",
      data: { "rcp_name": recipeTitle },
      success: function(array) {
        location.reload();
      }
    });
  });


  $('.recipeBlock').hide();
  $('.recipeTitle').click(function() {
    $(this).siblings('.recipeTitle').find('ul').slideUp();
    $(this).find('ul').slideToggle();
  });

  $(document).on("click", ".dropdown-menu > li > a.trigger", function(e) {
    var current=$(this).next();
    var grandparent=$(this).parent().parent();
    if ($(this).hasClass('left-caret') || $(this).hasClass('right-caret')) {
      $(this).toggleClass('right-caret left-caret');
    }
    grandparent.find('.left-caret').not(this).toggleClass('right-caret left-caret');
    grandparent.find(".sub-menu:visible").not(current).hide();
    current.toggle();
    e.stopPropagation();

    if (e.target.nextSibling.childElementCount == 0) {
      var super_categorie = e.target.getAttribute("value");
      /*   get subcategories for an ingredient to build dynamic dropdown menu   */
      $.ajax({
        type: "POST",
        url: "databaseQuerySubCategs.php",
        data: { "ing_super_categ": super_categorie },
        success: function(array) {
          var parsedData = jQuery.parseJSON(array);
          for (index = 1; index < parsedData.length; index++) {
            var ing_name = parsedData[index][0];
            var ing_is_final = parsedData[index][1];
            addMenuOption(e, ing_name, ing_is_final);
          }
        }
      });
    }
  });
  $(document).on("click", ".dropdown-menu > li > a:not(.trigger)", function() {
    var root = $(this).closest('.dropdown');
    root.find('.left-caret').toggleClass('right-caret left-caret');
    root.find('.sub-menu:visible').hide();
  });
});







/*   add a final ingredient to dynamic dropdown menu   */
/*   add a non final ingredient (i.e. which has sub-categories) to dynamic dropdown menu   */
function addMenuOption(ev, text, ing_is_final) {
  var ul = ev.target.parentNode.querySelector("ul");
  var li = document.createElement("li");
  var a = document.createElement("a");
  a.setAttribute("tabindex", "-1");
  a.setAttribute("href", "#");
  a.setAttribute("value", text);
  a.appendChild(document.createTextNode(text));
  li.appendChild(a);
  ul.appendChild(li);

  if (!ing_is_final) {
    var newUl = document.createElement("ul");
    newUl.className = "dropdown-menu sub-menu";
    a.className = "trigger right-caret";
    var span = document.createElement("span");
    span.className = "caret";
    a.appendChild(span);
    li.appendChild(newUl);
  } else {
    li.className = "menu";
  }
}


function filterRecipesOnType() {
    var input = document.getElementById("recipesSearchInput");
    filterRecipes(input.value);
}

function filterRecipes(rawFilter) {
  var filter = rawFilter.toLowerCase();
  var ul = document.getElementById("recipesList");
  var li = ul.getElementsByTagName("li");
  var i, a, txtValue;

  $.ajax({
    type: "POST",
    url: "getAllSuperCategories.php",
    data: { "filter_ing": filter },
    success: function(array) {
      var parsedData = jQuery.parseJSON(array);
      var lowerFilter;
      for (index = 1; index < parsedData.length; index++) {
        lowerFilter = parsedData[index].toLowerCase();
        if (lowerFilter.indexOf(filter) > -1) {
          $.ajax({
            type: "POST",
            url: "getRecipesContainingSuperCategory.php",
            data: { "filter_ing_super_categ": lowerFilter },
            success: function(array) {
              filterKeepRecipes(jQuery.parseJSON(array));
            }
          });
        }
      }
    }
  });
}



function filterKeepRecipes(keepRecipes) {
  var ul = document.getElementById("recipesList");
  var li = ul.getElementsByTagName("li");
  var i, a, txtValue;
  for (i = 0; i < li.length; i++) {
    if (li[i].childElementCount > 0) {
      li[i].style.display = "none";
      a = li[i].getElementsByTagName("a")[0];
      txtValue = a.textContent || a.innerText;
      for (index = 1; index < keepRecipes.length; index++) {
        if (txtValue.toLowerCase().indexOf(keepRecipes[index].toLowerCase()) > -1) {
          li[i].style.display = "";
        }
      }
    }
  }
}
