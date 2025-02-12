import React, { useState, useEffect, useRef } from 'react';
import { Stage, Layer, Text, Image, Transformer } from 'react-konva';

/** 
 * Composant pour afficher une image redimensionnable et transformable.
 * On lui ajoute également la gestion de la rotation.
 */
const URLImage = ({ element, isSelected, onSelect, onChange }) => {
  const shapeRef = useRef();
  const trRef = useRef();
  const [img, setImg] = useState(null);

  useEffect(() => {
    const i = new window.Image();
    i.src = element.src;
    i.crossOrigin = 'Anonymous';
    i.onload = () => setImg(i);
  }, [element.src]);

  useEffect(() => {
    if (isSelected && trRef.current && shapeRef.current) {
      trRef.current.nodes([shapeRef.current]);
      trRef.current.getLayer().batchDraw();
    }
  }, [isSelected]);

  return (
    <>
      <Image
        image={img}
        x={element.x}
        y={element.y}
        width={element.width}
        height={element.height}
        rotation={element.rotation || 0}
        draggable
        ref={shapeRef}
        onClick={onSelect}
        onTap={onSelect}
        onDragEnd={(e) => {
          onChange({
            ...element,
            x: e.target.x(),
            y: e.target.y(),
          });
        }}
        onTransformEnd={(e) => {
          const node = shapeRef.current;
          const scaleX = node.scaleX();
          const scaleY = node.scaleY();
          onChange({
            ...element,
            x: node.x(),
            y: node.y(),
            width: Math.max(5, node.width() * scaleX),
            height: Math.max(5, node.height() * scaleY),
            rotation: node.rotation(),
            scaleX: 1,
            scaleY: 1,
          });
        }}
      />
      {isSelected && (
        <Transformer
          ref={trRef}
          boundBoxFunc={(oldBox, newBox) => {
            if (newBox.width < 5 || newBox.height < 5) {
              return oldBox;
            }
            return newBox;
          }}
        />
      )}
    </>
  );
};

/**
 * Composant pour afficher du texte transformable.
 */
const ResizableText = ({ element, isSelected, onSelect, onChange }) => {
  const shapeRef = useRef();
  const trRef = useRef();

  useEffect(() => {
    if (isSelected && trRef.current && shapeRef.current) {
      trRef.current.nodes([shapeRef.current]);
      trRef.current.getLayer().batchDraw();
    }
  }, [isSelected]);

  return (
    <>
      <Text
        text={element.text}
        x={element.x}
        y={element.y}
        fontSize={element.fontSize}
        fill={element.fill}
        draggable
        rotation={element.rotation || 0}
        ref={shapeRef}
        onClick={onSelect}
        onTap={onSelect}
        onDblClick={() => {
          const newText = prompt('Modifier le texte', element.text);
          if (newText !== null) {
            onChange({ ...element, text: newText });
          }
        }}
        onDragEnd={(e) => {
          onChange({
            ...element,
            x: e.target.x(),
            y: e.target.y(),
          });
        }}
        onTransformEnd={(e) => {
          const node = shapeRef.current;
          const scaleX = node.scaleX();
          const scaleY = node.scaleY();
          onChange({
            ...element,
            x: node.x(),
            y: node.y(),
            fontSize: Math.max(5, element.fontSize * scaleY),
            rotation: node.rotation(),
            // Remise à l'échelle par défaut
            scaleX: 1,
            scaleY: 1,
          });
        }}
      />
      {isSelected && (
        <Transformer
          ref={trRef}
          enabledAnchors={['top-left', 'top-right', 'bottom-left', 'bottom-right']}
          boundBoxFunc={(oldBox, newBox) => {
            if (newBox.width < 5 || newBox.height < 5) {
              return oldBox;
            }
            return newBox;
          }}
        />
      )}
    </>
  );
};

const SandboxComponent = () => {
  const initialElements = window.__CANVAS_DATA__ ? window.__CANVAS_DATA__ : [];
  const [elements, setElements] = useState(initialElements);
  const [selectedId, setSelectedId] = useState(null);
  const stageRef = useRef();

  // Dimensions du canvas
  const [dimensions, setDimensions] = useState({
    width: window.innerWidth / 1.5,
    height: window.innerHeight,
  });
  useEffect(() => {
    const handleResize = () => {
      setDimensions({
        width: window.innerWidth / 1.5,
        height: window.innerHeight,
      });
    };
    window.addEventListener('resize', handleResize);
    return () => window.removeEventListener('resize', handleResize);
  }, []);

  // Boutons d'ajout en haut du canvas
  const addText = () => {
    const newText = {
      id: 'text_' + new Date().getTime(),
      type: 'text',
      x: 50,
      y: 50,
      text: 'Double-clique pour éditer',
      fontSize: 20,
      fill: 'black',
      draggable: true,
    };
    setElements((prev) => [...prev, newText]);
  };

  const addImage = (src) => {
    const newImage = {
      id: 'img_' + new Date().getTime(),
      type: 'image',
      x: 100,
      y: 100,
      src,
      width: 200,
      height: 150,
      rotation: 0,
      draggable: true,
    };
    setElements((prev) => [...prev, newImage]);
  };

  const handleImageUpload = (e) => {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = (evt) => {
      addImage(evt.target.result);
    };
    reader.readAsDataURL(file);
  };

  const addVideo = (src) => {
    const video = document.createElement('video');
    video.src = src;
    video.crossOrigin = 'Anonymous';
    video.loop = true;
    video.muted = true;
    video.play();
    const newVideo = {
      id: 'video_' + new Date().getTime(),
      type: 'video',
      x: 150,
      y: 150,
      videoElement: video,
      width: 300,
      height: 200,
      draggable: true,
    };
    setElements((prev) => [...prev, newVideo]);
  };

  const handleVideoUpload = (e) => {
    const file = e.target.files[0];
    if (!file) return;
    const url = URL.createObjectURL(file);
    addVideo(url);
  };

  // Dédésélection si clic en dehors d'un élément
  const handleStageMouseDown = (e) => {
    if (e.target === e.target.getStage()) {
      setSelectedId(null);
    }
  };

  // Met à jour un élément dans le tableau
  const updateElement = (id, newAttrs) => {
    const updatedElements = elements.map((el) =>
      el.id === id ? { ...el, ...newAttrs } : el
    );
    setElements(updatedElements);
  };

  // Récupérer l'élément sélectionné
  const selectedElement = elements.find((el) => el.id === selectedId);

  // Fonction pour enregistrer le canevas en BDD
  const saveCanvas = () => {
    const payload = {
      id: window.__CANVAS_ID__ || null, // On envoie l'ID du canevas s'il existe
      title: "Mon Canevas", // Tu peux prévoir un input pour que l'utilisateur choisisse le titre
      canvas: elements,     // On envoie l'état complet du canevas (tableau d'éléments)
    };

    let url = '/sandbox/save';
    if (window.__CANVAS_ID__) {
      url += '/' + window.__CANVAS_ID__;
    }

    fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(payload),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.status === 'success') {
          alert('Canevas enregistré avec l\'ID: ' + data.id);
        } else {
          alert('Erreur lors de l\'enregistrement : ' + data.message);
        }
      })
      .catch((err) => {
        console.error('Erreur:', err);
        alert('Erreur lors de l\'enregistrement');
      });
  };

  return (
    <div>
      {/* Boutons d'ajout */}
      <div style={{ marginBottom: 10 }}>
        <button className="btn btn-success" onClick={addText}>Ajouter du texte</button>
        <input className="d-inline w-25 form-control form-control-sm" type="file" accept="image/*" onChange={handleImageUpload} />
        <input className="d-inline w-25 form-control form-control-sm" type="file" accept="video/*" onChange={handleVideoUpload} />
        {/* Bouton pour enregistrer le canevas */}
        <button className="btn btn-primary" onClick={saveCanvas} style={{ marginLeft: 10 }}>
          Enregistrer le canevas
        </button>
      </div>

      {/* Canvas Konva */}
      <Stage
        width={dimensions.width}
        height={dimensions.height}
        ref={stageRef}
        onMouseDown={handleStageMouseDown}
        onTouchStart={handleStageMouseDown}
      >
        <Layer>
          {elements.map((el) => {
            if (el.type === 'text') {
              return (
                <ResizableText
                  key={el.id}
                  element={el}
                  isSelected={el.id === selectedId}
                  onSelect={() => setSelectedId(el.id)}
                  onChange={(newAttrs) => updateElement(el.id, newAttrs)}
                />
              );
            } else if (el.type === 'image') {
              return (
                <URLImage
                  key={el.id}
                  element={el}
                  isSelected={el.id === selectedId}
                  onSelect={() => setSelectedId(el.id)}
                  onChange={(newAttrs) => updateElement(el.id, newAttrs)}
                />
              );
            } else if (el.type === 'video') {
              // Pour la vidéo, on pourra créer un composant similaire inspiré d'URLImage.
              // Ici, on ne l'affiche pas dans ce panneau de transformation.
              return null;
            }
            return null;
          })}
        </Layer>
      </Stage>

      {/* Panneau de personnalisation de l'affichage pour l'élément sélectionné */}
      {
        selectedElement && (
          <div
            style={{
              position: 'fixed',
              bottom: 45,
              left: 10,
              background: 'rgba(255, 255, 255, 0.9)',
              padding: '10px',
              borderRadius: '5px',
              boxShadow: '0px 0px 5px rgba(0,0,0,0.3)',
            }}
          >
            <h4>Personnalisation de l'élément</h4>
            {selectedElement.type === 'text' && (
              <div>
                <button
                  onClick={() =>
                    updateElement(selectedId, {
                      fontSize: selectedElement.fontSize + 2,
                    })
                  }
                >
                  Augmenter la taille
                </button>
                <button
                  onClick={() =>
                    updateElement(selectedId, {
                      fontSize: Math.max(selectedElement.fontSize - 2, 10),
                    })
                  }
                >
                  Diminuer la taille
                </button>
                <label style={{ marginLeft: 10 }}>
                  Couleur :
                  <input
                    type="color"
                    value={selectedElement.fill || '#000000'}
                    onChange={(e) =>
                      updateElement(selectedId, { fill: e.target.value })
                    }
                    style={{ marginLeft: 5 }}
                  />
                </label>
              </div>
            )}
            {selectedElement.type === 'image' && (
              <div>
                <button
                  onClick={() =>
                    updateElement(selectedId, {
                      rotation: (selectedElement.rotation || 0) + 15,
                    })
                  }
                >
                  Tourner +15°
                </button>
                <button
                  onClick={() =>
                    updateElement(selectedId, {
                      rotation: (selectedElement.rotation || 0) - 15,
                    })
                  }
                >
                  Tourner -15°
                </button>
              </div>
            )}
          </div>
        )
      }
    </div>
  );
};

export default SandboxComponent;
