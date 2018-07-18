// var sumStats = [13.1, 12.3, 11.0, 12.1, 18.2, 21.0, 24.0];
// var statsText = ['x', 'y', 'z', 'a', 'b', 'y', 'u'];

 function barGraph(values, text){
  var width = 600,
  barHeight = 22;

  var x = d3.scaleLinear()
      .domain([0, d3.max(values)])
      .range([0, width]);

  //set svg
  var chart = d3.select(".chart")
      .attr("width", width)
      .attr("height", barHeight * values.length);

  //prepare chart length
  var bar = chart.selectAll("g")
      .data(values)
      .enter().append("g")
      .attr("transform", function(d, i) { return "translate(0," + i * barHeight + ")"; });

  //append the svg rectangles
  bar.append("rect")
      .attr("width", x)
      .attr("height", barHeight - 10)
      .style('fill', '#f05742');

  //append the percentages (change the text here)
  bar.append("text")
      .data(text)
      .attr("x", function(d) { return 20; }) //position
      .attr("y", barHeight / 2)
      .attr("dy", "-.2em") //y position of text
      //display 1 decimal
      .text(function(text){return text;})
      .style("font-size", '9px');

  //append the title
  bar.append("text")           
          .attr("y", barHeight*values.length + 2)
          .attr("dy", "-.3em") 
          .style("font-size", "10px") 
          .style("fill", "#faccc6")  
          .text("% of area that is settlement");
  };


  //this function load summary stats on load
    function getScores(){
      var SQLquery2 = "SELECT player1, sum(area) FROM data_game GROUP BY player1";
      var names=[];
      var scores=[];

       $.getJSON("https://"+cartoDBusername+".carto.com/api/v2/sql?format=JSON&q="+SQLquery2, function(data){
          //console.log('this is the new query', data.rows.length);
          //console.log('this is the new query', data.rows[0]);
          //console.log('this is the new query', data.rows[0].player1.length);
         for (var i=0;i<data.rows.length; i++){
            names.push(data.rows[i].player1);
            scores.push(data.rows[i].sum);
         }
         //console.log(names);
          barGraph(scores, names);

      });
    };


