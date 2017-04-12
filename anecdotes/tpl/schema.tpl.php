<?xml version="1.0" encoding="UTF-8"?>
<?xml-stylesheet type="text/xsl" href="../xrem/rng2html.xsl"?>
<grammar xmlns="http://relaxng.org/ns/structure/1.0" xmlns:a="http://relaxng.org/ns/compatibility/annotations/1.0" xmlns:h="http://www.w3.org/1999/xhtml" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" datatypeLibrary="http://www.w3.org/2001/XMLSchema-datatypes" ns="http://www.tei-c.org/ns/1.0" xml:lang="fr">

  <!--
    SURCHARGE TEIBOOK
    http://relaxng.org/tutorial-20011203.html#IDAVEZR
  -->
  <include href="../../Teinte/teinte.rng" h:href="../../Teinte/teinte.rng" h:title="Schéma Teinte">

    <define name="milestone">
      <element name="milestone">
        <a:documentation>Borne anonyme, vide, permet d’étiqueter des points qui n’entrent pas dans la hiérarchie éditoriale, et qui ne sont pas des sauts de page <a:el>pb</a:el>, de colonne <a:el>cb</a:el> ou de ligne <a:el>lb</a:el></a:documentation>
        <empty/>
        <choice>
          <!-- anecdoteStart -->
          <group>
            <attribute name="type">
              <value>anecdoteStart</value>
              <a:documentation>Début d’une anecdote</a:documentation>
            </attribute>
            <attribute name="xml:id">
              <choice><?php foreach($anecdotes as $key => $anecdote){?>
                <value><?php echo $key; ?></value>
                <a:documentation><?php echo $anecdote["title"]; ?></a:documentation>
                <?php }?></choice>
            </attribute>
          </group>
          <!-- commentStart -->
          <group>
            <attribute name="type">
              <value>commentStart</value>
            </attribute>
            <attribute name="corresp">
              <choice><?php foreach($anecdotes as $key => $anecdote){?>
                <value>#<?php echo $key; ?></value>
                <?php }?></choice>
          </attribute>
          </group>
        </choice>
      </element>
    </define>

  </include>

  <!--
    SPÉCIFIQUE ANECDOTES
  -->


</grammar>
                