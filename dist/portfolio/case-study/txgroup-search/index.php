<?php include $_SERVER['DOCUMENT_ROOT'].'/includes/header.php' ?>
<main class="largeContainer">
  <article class="case--study_container columns two-col">
    <aside class="col">
      <img src="/images/case-study/txgroup-search/logo.png" alt="Tx Group logo" title="Tx Group logo">
      <img src="/images/case-study/txgroup-search/laptop-search-one.jpg" alt="Tx Group desktop site" title="Tx Group desktop site">
      <img src="/images/case-study/txgroup-search/laptop-search-two.jpg" alt="Tx Group mobile site" title="Tx Group mobile site">
    </aside>
    <section class="col">
      <div class="content">
        <h1>Tx Group search</h1>
        <p><i>Tx Group search aims to provide a more customized experience for visitors of any Tx Group site.</i></p>
        <p>
          <a href="https://txgroup.co.uk" target="_blank">Tx Group</a> is a corporation which houses approximately 10+ Healthcare companies which can gather around 500,000 visitors per year. The challenge was to develop a centralized search system, which can allow visitors to search the site they are currently on, or all sites under Tx Group.
        </p>
        <p>
          The system works by using a sitemap.xml used to index pages into Google. Using this file, the system is able to scrape the content of all the pages within this file. This uses <a href="http://simplehtmldom.sourceforge.net/">simple html dom</a> to filter out the content required to be stored into a db.
        </p>
        <p>
          The content is then checked for any special characters, which will be slashed out before adding to the db. Users are able to run a search query dependant on the site they're on. Results will then be parsed and returned into json format which can then be interpreted into a clean front-end Google results style solution.
        </p>
        <p>Find a live example <a href="https://www.sltforkids.co.uk/search-results/" target="_blank">Here</a>!</p>
        <div class="details flex flex-column">
          <div class="flex flex-row">
            <h3>Created</h3>
            <p>February 2018</p>
          </div>
          <div class="flex flex-row ">
            <h3>Skills</h3>
            <p> SQL<br>DB<br>Security</p>
          </div>

          <!-- <p><b>Website</b> <a href="http://oakleyandfriends.co.uk">www.oakleyandfriends.co.uk</a></p>
          <p><b>Created</b> September 2017</p>
          <p><b>Features</b> Sitecake, Wordpress</p> -->
        </div>
      </div>
    </section>
  </article>
  <article>
    <div class="smallContainer">
      <div class="smallContainer">
        <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/contact-form.php';?>
      </div>
    </div>
  </article>
</main>
<?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.php' ?>
