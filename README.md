## Playable City: Why do we need a reliable database for vacant land?

According to think tank New Economic Foundation (NEF), groups looking to build community-led, affordable housing development in their area face one great obstacle: acquiring land (Martin, 2018). Many reasons prevent community groups from entering this game. Some of them relate to the lack of transparency when it comes to available ressources:

*1) Inaccessibility of data:* <br> Land ownership data is restricted to public. Access to the LM Land Registry is not free of charge and can only be obtained for individual properties (Shrubsole, 2016).

*2) Reliability of data:* <br> The redevelopment of brownfield sites and empty property is greatly constrained by a paucity of data (Myers and Wyatt, 2004). Research by the author has shown that although databases exist for both commercial and governmental property ownership and vacant land, both databases are erroneous, outdated and incomplete. In addition, about 18% of land in England and Wales is unregistered, and not even the government knows who owns it (Hookway, 2018).

## Scope of this project
Two pieces of information are essential for community groups interested in building: knowing the location of vacant land and knowing by whom it is owned. This thesis aims to explore gamified crowd sourced data collection methods to acquire data about land ownership and land vacancy. The methods will be tested in a proof-of-concept prototype. Previous studies highlight the following advantages of crowd sourcing:

*1) Crowd sourced data collection:* <br> Collecting data through administrational bodies takes a long time whereas a crowd can rapidly generate data about circumstances affecting the crowd itself at little or no cost (Barbier et al., 2012). Based on the assumption that data about ownership and vacancy is outdated quickly, crowdsourcing methods have great potential.

*2) Gamification in spatial processes:* <br> Krek (2005) observed that many citizens are “rationally ignorant”. According to the rational ignorance condition, citizens tend to ignore urban planning processes because participation would require a high investment of time and effort to ascertain the current planning situation. Poplin (2012), tries to overcome this condition by creating online serious games to bring playfulness and pleasure to the serious processes of urban planning decisions.
Case study background

## Vacant land in London
In the year 2000, the government released a White Paper where councils were encouraged to use previously developed land and buildings are regenerated to maximize the use of existing resources (Davies et al., 2001). Using existing resources is important due to planning policies that restrict development on remaining greenfields and limitations relating to land ownership. However, the redevelopment of previously developed land, also referred to as brownfields, and empty property is greatly constrained by a lack of data (Myers and Wyatt, 2004). The following paragraphs describe the information that is publicly available and its deficits.

*1) National Land Use Database of Previously Developed Land (2017)*<br> 
The government has undertaken some efforts to provide a database of vacant land across England. The database NLUD-PDL, released by Homes and Communities Agency (HCA) in 2017 and part of the general National Land Use Database, classifies land into vacant buildings and previously developed land. It contains around 8000 entries including area size and point coordinates. As caveats, the authorities state that the dataset was never validated. In addition, out of the 326 local planning authorities in England, excluding National Parks, only 149 (45%) provided information.

*2) London Brownfield Sites Database (2009)*<br> 
The London Development Agency (LDA) and the Homes and Communities Agency (HCA) released a geospatial database of London’s brownfield land over 0.25ha. However, on the website it is stated that the brownfield sites dataset was integrated in the NLUD-PDL. Hence, will only be accessible as point data. Old versions including polygons can still be accessed from the London Data Store.


EDIT: Just recently, the online news platform Citylab published an interesting article on the topic of land vacancy, land banking and property guardianship. Read here: https://www.citylab.com/solutions/2018/10/londons-empty-spaces/572011/

## Prototype
https://play.imfeld.info/

## File structure
index.php - login page <br>
map.php - main page including interactive mapping <br>
built.php - overview and building element<br>
rules.php - game mechanisms and rules<br>
logout.php - logout and session destroyed<br>

## Built With

* [Carto](https://carto.com/) - Database management (Carto account required!)
* [Leaflet draw](https://github.com/Leaflet/Leaflet.draw) - User web mapping
* [Jquery](https://api.jquery.com/)
* [Bootstrap](https://v4-alpha.getbootstrap.com/) - Static framework
* [D3](https://d3js.org/) - Graphs

## Acknowledgments

* [MIT DUSPVIZ](http://duspviz.mit.edu/)

## License
MIT License
