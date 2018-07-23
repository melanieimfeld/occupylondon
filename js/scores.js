// var sumStats = [13.1, 12.3, 11.0, 12.1, 18.2, 21.0, 24.0];
// var statsText = ['x', 'y', 'z', 'a', 'b', 'y', 'u'];
//var width = $('chart1').width();
//var width1 = document.getElementById('chart1').clientWidth;
//  var width = document.getElementById('chart1').clientWidth;

// window.onresize = function() {
//       width = document.getElementById('chart1').clientWidth;
//        //console.log('flexwidth', width);
//   };

 function barGraph(values, text, color){

  // var width = 600,
  var width = document.getElementById('chart1').clientWidth-20,
  barHeight = 20;
  

// pass a function to map (scale values)
  var values2 = values.map(x => x * 0.01);
  console.log(values2);
  console.log(values);

  var x = d3.scaleLinear()
      .domain([0, d3.max(values2)])
      .range([0, width]);
  
  console.log(x);
  //set svg
  var chart = d3.select(".chart")
      .attr("width", width)
      .attr("height", barHeight * values2.length+20); 

  //prepare chart length
  var bar = chart.selectAll("g")
      .data(values2)
      .enter().append("g")
      .attr("transform", function(d, i) { return "translate(0," + i * barHeight + ")"; });

  //append the svg rectangles
  bar.append("rect")
      .attr("width", x)
      .attr("height", barHeight-10) //this is the thickness of bars
      // .style('fill', '#f05742');
      .data(color)
      .style("fill", function(color) {return color});

  //append the text here
  bar.append("text")
      .data(text)
      .attr("x", function(d) { return 20; }) //position
      .attr("y", barHeight / 2)
      .attr("dy", "-.2em") //y position of text
      //display 1 decimal
      .text(function(text, i){return text})
      .style("font-size", '9px')
      .style("fill", "white");

  //append the title
  // bar.append("text")           
  //         .attr("y", barHeight*values2.length + 2)
  //         .attr("dy", "-.3em") 
  //         .style("font-size", "10px") 
  //         .style("fill", "#faccc6")  
  //         .text("% of area that is settlement");
  };


//this function load summary stats on load
function getScores(){
  var SQLquery2 = "SELECT current_owner,playercolor,sum(area) FROM data_game GROUP BY current_owner, playercolor";
  var names=[];
  var scores=[];
  var colors=[];
  //reset tokens if user has more than one flag on a lot?

   $.getJSON("https://"+cartoDBusername+".carto.com/api/v2/sql?format=JSON&q="+SQLquery2, function(data){
      //console.log('this is the new query', data.rows.length);
      //console.log('this is the new query', data.rows[0]);
      //console.log('this is the new query', data.rows[0].player1.length);
     for (var i=0;i<data.rows.length; i++){
        names.push(data.rows[i].current_owner);
        scores.push(data.rows[i].sum);
        colors.push(data.rows[i].playercolor);
     }
     console.log('those are the colors', colors);
      barGraph(scores,names,colors);

  });
};


